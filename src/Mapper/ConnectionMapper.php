<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Mapper;

use Sylius\Component\Core\Model\ChannelInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\Connection;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ConnectionInterface;
use Webmozart\Assert\Assert;

final class ConnectionMapper implements ConnectionMapperInterface
{
    public function mapFromChannel(ChannelInterface $channel): ConnectionInterface
    {
        $channelCode = $channel->getCode();
        Assert::notNull($channelCode, 'The channel does not have a code.');

        return new Connection(
            'sylius',
            $channelCode,
            $channel->getName() ?? 'Sylius eCommerce'
        );
    }
}
