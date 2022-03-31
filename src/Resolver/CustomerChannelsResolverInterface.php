<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Resolver;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;

interface CustomerChannelsResolverInterface
{
    /**
     * @return ChannelInterface[]
     */
    public function resolve(CustomerInterface $customer): array;
}
