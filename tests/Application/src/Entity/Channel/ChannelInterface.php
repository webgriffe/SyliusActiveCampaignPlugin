<?php

declare(strict_types=1);

namespace App\Entity\Channel;

use Sylius\Component\Core\Model\ChannelInterface as BaseChannelInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface;

interface ChannelInterface extends BaseChannelInterface, ActiveCampaignAwareInterface
{
}
