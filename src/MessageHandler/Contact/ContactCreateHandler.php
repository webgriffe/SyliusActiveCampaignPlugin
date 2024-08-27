<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\Contact;

use InvalidArgumentException;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignResourceClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Mapper\ContactMapperInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Contact\ContactCreate;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Contact\ContactResponse;

final class ContactCreateHandler
{
    public function __construct(
        private ContactMapperInterface $contactMapper,
        private ActiveCampaignResourceClientInterface $activeCampaignContactClient,
        private CustomerRepositoryInterface $customerRepository,
    ) {
    }

    public function __invoke(ContactCreate $message): void
    {
        $customerId = $message->getCustomerId();
        /** @var CustomerInterface|null $customer */
        $customer = $this->customerRepository->find($customerId);
        if ($customer === null) {
            throw new InvalidArgumentException(sprintf('Customer with id "%s" does not exists', $customerId));
        }
        if (!$customer instanceof ActiveCampaignAwareInterface) {
            throw new InvalidArgumentException(sprintf('The Customer entity should implement the "%s" class', ActiveCampaignAwareInterface::class));
        }

        $activeCampaignId = $customer->getActiveCampaignId();
        if ($activeCampaignId !== null) {
            throw new InvalidArgumentException(sprintf('The Customer with id "%s" has been already created on ActiveCampaign on the contact with id "%s"', $customerId, $activeCampaignId));
        }
        try {
            $createContactResponse = $this->activeCampaignContactClient->create($this->contactMapper->mapFromCustomer($customer));
            $activeCampaignContactId = $createContactResponse->getResourceResponse()->getId();
        } catch (UnprocessableEntityHttpException $e) {
            // If validation fails try to check if contact already exists
            $searchContactsForEmail = $this->activeCampaignContactClient->list(['email' => (string) $customer->getEmail()])->getResourceResponseLists();
            if (count($searchContactsForEmail) < 1) {
                throw $e;
            }
            /** @var ContactResponse $contact */
            $contact = reset($searchContactsForEmail);
            $activeCampaignContactId = $contact->getId();
        }
        $customer->setActiveCampaignId($activeCampaignContactId);
        $this->customerRepository->add($customer);
    }
}
