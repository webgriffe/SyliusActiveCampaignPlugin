<?php

declare(strict_types=1);

namespace App\Doctrine\ORM;

use Sylius\Bundle\CoreBundle\Doctrine\ORM\OrderRepository as BaseOrderRepository;
use Webgriffe\SyliusActiveCampaignPlugin\Doctrine\ORM\ActiveCampaignResourceRepositoryTrait;
use Webgriffe\SyliusActiveCampaignPlugin\Repository\ActiveCampaignAwareRepositoryInterface;

final class OrderRepository extends BaseOrderRepository implements ActiveCampaignAwareRepositoryInterface
{
    use ActiveCampaignResourceRepositoryTrait;
}
