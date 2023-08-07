<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusActiveCampaignPlugin\App\Entity\Channel;

use Sylius\Component\Core\Model\ChannelInterface as BaseChannelInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ChannelActiveCampaignAwareInterface;

interface ChannelInterface extends BaseChannelInterface, ChannelActiveCampaignAwareInterface
{
}
