<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Resolver;

use Webgriffe\SyliusActiveCampaignPlugin\Exception\ChannelListIdDoesNotExistException;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ContactListInterface;

final class CustomerBasedListSubscriptionStatusResolver implements ListSubscriptionStatusResolverInterface
{
    public function resolve($customer, $channel): int
    {
        $listId = $channel->getActiveCampaignListId();
        if ($listId === null) {
            throw new ChannelListIdDoesNotExistException(sprintf('The channel "%s" does not have a list id.', (string) $channel->getCode()));
        }

        return $customer->isSubscribedToNewsletter() ? ContactListInterface::SUBSCRIBED_STATUS_CODE : ContactListInterface::UNSUBSCRIBED_STATUS_CODE;
    }
}
