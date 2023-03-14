<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Updater;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ContactListInterface;

final class CustomerBasedListSubscriptionStatusUpdater implements ListSubscriptionStatusUpdaterInterface
{
    public function __construct(
        private CustomerRepositoryInterface $customerRepository,
    ) {
    }

    public function update(CustomerInterface $customer, ChannelInterface $channel, int $listSubscriptionStatus): void
    {
        $customer->setSubscribedToNewsletter($listSubscriptionStatus === ContactListInterface::SUBSCRIBED_STATUS_CODE);
        $this->customerRepository->add($customer);
    }
}
