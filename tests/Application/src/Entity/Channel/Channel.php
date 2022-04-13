<?php

declare(strict_types=1);

namespace App\Entity\Channel;

use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Core\Model\Channel as BaseChannel;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareTrait;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ChannelActiveCampaignAwareTrait;

/**
 * @ORM\Entity
 * @ORM\Table(name="sylius_channel")
 */
class Channel extends BaseChannel implements ChannelInterface
{
    use ActiveCampaignAwareTrait;
    use ChannelActiveCampaignAwareTrait;
}
