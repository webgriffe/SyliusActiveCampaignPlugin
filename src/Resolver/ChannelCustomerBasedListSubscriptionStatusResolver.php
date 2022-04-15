<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Resolver;

use Webgriffe\SyliusActiveCampaignPlugin\Exception\ChannelCustomerDoesNotExistException;
use Webgriffe\SyliusActiveCampaignPlugin\Exception\ChannelListIdDoesNotExistException;
use Webgriffe\SyliusActiveCampaignPlugin\Exception\CustomerListSubscriptionStatusNotDefinedException;

final class ChannelCustomerBasedListSubscriptionStatusResolver implements ListSubscriptionStatusResolverInterface
{
    public function resolve($customer, $channel): int
    {
        $listId = $channel->getActiveCampaignListId();
        if ($listId === null) {
            throw new ChannelListIdDoesNotExistException(sprintf('The channel "%s" does not have a list id.', (string) $channel->getCode()));
        }
        if (null === ($channelCustomer = $customer->getChannelCustomerByChannel($channel))) {
            throw new ChannelCustomerDoesNotExistException(sprintf('The customer "%s" is not related with the channel "%s".', (string) $customer->getEmail(), (string) $channel->getCode()));
        }
        $listSubscriptionStatus = $channelCustomer->getListSubscriptionStatus();
        if ($listSubscriptionStatus === null) {
            throw new CustomerListSubscriptionStatusNotDefinedException(sprintf('The list subscription status for list of channel "%s" of the customer "%s" is not defined.', (string) $channel->getCode(), (string) $customer->getEmail()));
        }

        return $listSubscriptionStatus;
    }
}
