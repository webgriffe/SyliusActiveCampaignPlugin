<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Enqueuer;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignResourceClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Contact\ContactCreate;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Contact\ContactUpdate;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Contact\ContactResponse;
use Webmozart\Assert\Assert;

final class ContactEnqueuer implements ContactEnqueuerInterface
{
    public function __construct(
        private MessageBusInterface $messageBus,
        private ActiveCampaignResourceClientInterface $activeCampaignContactClient,
        private EntityManagerInterface $entityManager
    ) {
    }

    public function enqueue($customer): void
    {
        /** @var string|int|null $customerId */
        $customerId = $customer->getId();
        Assert::notNull($customerId, 'The customer id should not be null');
        $activeCampaignContactId = $customer->getActiveCampaignId();
        if ($activeCampaignContactId !== null) {
            $this->messageBus->dispatch(new ContactUpdate($customerId, $activeCampaignContactId));

            return;
        }
        $email = $customer->getEmail();
        Assert::notNull($email, 'The customer email should not be null');
        $searchContactsForEmail = $this->activeCampaignContactClient->list(['email' => $email])->getResourceResponseLists();
        if (count($searchContactsForEmail) > 0) {
            /** @var ContactResponse $contact */
            $contact = reset($searchContactsForEmail);
            $activeCampaignContactId = $contact->getId();
            $customer->setActiveCampaignId($activeCampaignContactId);
            $this->entityManager->flush();

            $this->messageBus->dispatch(new ContactUpdate($customerId, $activeCampaignContactId));

            return;
        }

        $this->messageBus->dispatch(new ContactCreate($customerId));
    }
}
