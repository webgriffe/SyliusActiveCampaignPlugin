<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Mapper;

use Sylius\Component\Core\Model\ChannelInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign\ConnectionFactoryInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ConnectionInterface;
use Webmozart\Assert\Assert;

final class ConnectionMapper implements ConnectionMapperInterface
{
    public function __construct(
        private ConnectionFactoryInterface $connectionFactory
    ) {
    }

    public function mapFromChannel(ChannelInterface $channel): ConnectionInterface
    {
        $channelCode = $channel->getCode();
        Assert::notNull($channelCode, 'The channel does not have a code.');

        return $this->connectionFactory->createNew(
            'sylius',
            $channelCode,
            $channel->getName() ?? 'Sylius eCommerce'
        );
    }
}
