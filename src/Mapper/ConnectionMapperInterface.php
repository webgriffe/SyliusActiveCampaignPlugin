<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Mapper;

use Sylius\Component\Core\Model\ChannelInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ConnectionInterface;

interface ConnectionMapperInterface
{
    public function mapFromChannel(ChannelInterface $channel): ConnectionInterface;
}
