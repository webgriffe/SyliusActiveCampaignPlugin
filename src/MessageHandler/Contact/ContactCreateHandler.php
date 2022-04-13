<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\Contact;

use InvalidArgumentException;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignResourceClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Mapper\ContactMapperInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Contact\ContactCreate;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Contact\ContactTagsAdder;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface;

final class ContactCreateHandler
{
    public function __construct(
        private ContactMapperInterface $contactMapper,
        private ActiveCampaignResourceClientInterface $activeCampaignContactClient,
        private CustomerRepositoryInterface $customerRepository,
        private MessageBusInterface $messageBus
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
        $createContactResponse = $this->activeCampaignContactClient->create($this->contactMapper->mapFromCustomer($customer));
        $customer->setActiveCampaignId($createContactResponse->getResourceResponse()->getId());
        $this->customerRepository->add($customer);

        $this->messageBus->dispatch(new ContactTagsAdder($customerId));
    }
}
