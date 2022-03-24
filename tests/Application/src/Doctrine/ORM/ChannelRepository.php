<?php

declare(strict_types=1);

namespace App\Doctrine\ORM;

use Sylius\Bundle\ChannelBundle\Doctrine\ORM\ChannelRepository as BaseChannelRepository;
use Webgriffe\SyliusActiveCampaignPlugin\Doctrine\ORM\ChannelActiveCampaignAwareRepositoryTrait;
use Webgriffe\SyliusActiveCampaignPlugin\Repository\ChannelActiveCampaignAwareRepositoryInterface;

class ChannelRepository extends BaseChannelRepository implements ChannelActiveCampaignAwareRepositoryInterface
{
    use ChannelActiveCampaignAwareRepositoryTrait;
}
