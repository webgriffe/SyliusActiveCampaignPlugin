<?php

declare(strict_types=1);

namespace App\Doctrine\ORM;

use Sylius\Bundle\ChannelBundle\Doctrine\ORM\ChannelRepository as BaseChannelRepository;
use Webgriffe\SyliusActiveCampaignPlugin\Doctrine\ORM\ActiveCampaignCustomerRepositoryTrait;
use Webgriffe\SyliusActiveCampaignPlugin\Repository\ActiveCampaignAwareRepositoryInterface;

class ChannelRepository extends BaseChannelRepository implements ActiveCampaignAwareRepositoryInterface
{
    use ActiveCampaignCustomerRepositoryTrait;
}
