<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Enqueuer;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
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
        private EntityManagerInterface $entityManager,
        private ?LoggerInterface $logger = null,
    ) {
        if ($this->logger === null) {
            trigger_deprecation(
                'webgriffe/sylius-active-campaign-plugin',
                'v0.12.2',
                'The logger argument is mandatory.',
            );
        }
    }

    public function enqueue($customer): void
    {
        /** @var string|int|null $customerId */
        $customerId = $customer->getId();
        Assert::notNull($customerId, 'The customer id should not be null');
        $this->logger?->debug(sprintf(
            'Starting enqueuing contact for customer "%s".',
            $customerId,
        ));
        $activeCampaignContactId = $customer->getActiveCampaignId();
        if ($activeCampaignContactId !== null) {
            $this->logger?->debug(sprintf(
                'Customer "%s" has an already valued ActiveCampaign id "%s", so we have to update the contact.',
                $customerId,
                $activeCampaignContactId,
            ));
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
            $this->logger?->debug(sprintf(
                'Customer "%s" has an already valued ActiveCampaign id "%s", so we have to update the contact.',
                $customerId,
                $activeCampaignContactId,
            ));

            $this->messageBus->dispatch(new ContactUpdate($customerId, $activeCampaignContactId));

            return;
        }
        $this->logger?->debug(sprintf(
            'No contact found for given customer "%s", we have to create the contact.',
            $customerId,
        ));

        $this->messageBus->dispatch(new ContactCreate($customerId));
    }
}
