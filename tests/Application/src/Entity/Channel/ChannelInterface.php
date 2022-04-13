<?php

declare(strict_types=1);

namespace App\Entity\Channel;

use Sylius\Component\Core\Model\ChannelInterface as BaseChannelInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ChannelActiveCampaignAwareInterface;

interface ChannelInterface extends BaseChannelInterface, ChannelActiveCampaignAwareInterface
{
}
