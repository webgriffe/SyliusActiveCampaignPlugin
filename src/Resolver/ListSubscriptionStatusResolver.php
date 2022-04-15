<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Resolver;

use Webgriffe\SyliusActiveCampaignPlugin\Model\ChannelCustomerInterface;

final class ListSubscriptionStatusResolver implements ListSubscriptionStatusResolverInterface
{
    public function resolve($customer, $channel): bool
    {
        $listId = $channel->getActiveCampaignListId();
        if ($listId === null) {
            return false;
        }
        if (null === ($channelCustomer = $customer->getChannelCustomerByChannel($channel))) {
            return false;
        }
        if ($channelCustomer->getListSubscriptionStatus() !== ChannelCustomerInterface::SUBSCRIBED_TO_CONTACT_LIST) {
            return false;
        }

        return true;
    }
}
