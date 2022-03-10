<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\MessageHandler;

use InvalidArgumentException;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Mapper\ContactMapperInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Message\ContactCreate;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface;

final class ContactCreateHandler
{
    public function __construct(
        private ContactMapperInterface $contactMapper,
        private ActiveCampaignClientInterface $activeCampaignClient,
        private CustomerRepositoryInterface $customerRepository
    ) {
    }

    public function __invoke(ContactCreate $message): void
    {
        /** @var CustomerInterface|null $customer */
        $customer = $this->customerRepository->find($message->getCustomerId());
        if ($customer === null) {
            //TODO: Delete the customer?
            return;
        }
        if (!$customer instanceof ActiveCampaignAwareInterface) {
            throw new InvalidArgumentException(sprintf('The Customer entity should implement the "%s" class', ActiveCampaignAwareInterface::class));
        }

        if ($customer->getActiveCampaignId() !== null) {
            //TODO: Throw?

            return;
        }
        $this->activeCampaignClient->createContact($this->contactMapper->mapFromCustomer($customer));
    }
}
