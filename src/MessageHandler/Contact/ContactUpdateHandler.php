<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\Contact;

use InvalidArgumentException;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Mapper\ContactMapperInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Contact\ContactUpdate;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface;

final class ContactUpdateHandler
{
    public function __construct(
        private ContactMapperInterface $contactMapper,
        private ActiveCampaignClientInterface $activeCampaignClient,
        private CustomerRepositoryInterface $customerRepository
    ) {
    }

    public function __invoke(ContactUpdate $message): void
    {
        $customerId = $message->getCustomerId();
        /** @var CustomerInterface|null $customer */
        $customer = $this->customerRepository->find($customerId);
        if ($customer === null) {
            throw new InvalidArgumentException(sprintf('Customer with id "%s" does not exists.', $customerId));
        }
        if (!$customer instanceof ActiveCampaignAwareInterface) {
            throw new InvalidArgumentException(sprintf('The Customer entity should implement the "%s" class.', ActiveCampaignAwareInterface::class));
        }

        $activeCampaignId = $customer->getActiveCampaignId();
        if ($activeCampaignId !== $message->getActiveCampaignId()) {
            throw new InvalidArgumentException(sprintf('The customer with id "%s" has an ActiveCampaign id that doe not match. Expected "%s", given "%s".', $customerId, $message->getActiveCampaignId(), (string) $activeCampaignId));
        }
        $this->activeCampaignClient->updateContact($message->getActiveCampaignId(), $this->contactMapper->mapFromCustomer($customer));
    }
}
