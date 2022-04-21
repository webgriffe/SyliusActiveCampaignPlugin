<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Updater;

use InvalidArgumentException;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\CustomerActiveCampaignAwareInterface;

final class ChannelCustomerBasedListSubscriptionStatusUpdater implements ListSubscriptionStatusUpdaterInterface
{
    public function __construct(
        private RepositoryInterface $channelCustomerRepository
    ) {
    }

    public function update(CustomerInterface $customer, ChannelInterface $channel, int $listSubscriptionStatus): void
    {
        if (!$customer instanceof CustomerActiveCampaignAwareInterface) {
            throw new InvalidArgumentException(sprintf('The customer should implements the "%s" interface.', CustomerActiveCampaignAwareInterface::class));
        }
        $channelCustomer = $customer->getChannelCustomerByChannel($channel);
        if ($channelCustomer === null) {
            throw new InvalidArgumentException(sprintf('The customer "%s" does not have an association with the channel "%s".', (string) $customer->getEmail(), (string) $channel->getCode()));
        }
        $channelCustomer->setListSubscriptionStatus($listSubscriptionStatus);
        $this->channelCustomerRepository->add($channelCustomer);
    }
}
