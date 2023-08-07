<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusActiveCampaignPlugin\App\Doctrine\ORM;

use Sylius\Bundle\ChannelBundle\Doctrine\ORM\ChannelRepository as BaseChannelRepository;
use Webgriffe\SyliusActiveCampaignPlugin\Doctrine\ORM\ActiveCampaignChannelRepositoryTrait;
use Webgriffe\SyliusActiveCampaignPlugin\Repository\ActiveCampaignResourceRepositoryInterface;

class ChannelRepository extends BaseChannelRepository implements ActiveCampaignResourceRepositoryInterface
{
    use ActiveCampaignChannelRepositoryTrait;
}
