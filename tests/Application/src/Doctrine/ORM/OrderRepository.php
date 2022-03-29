<?php

declare(strict_types=1);

namespace App\Doctrine\ORM;

use Sylius\Bundle\CoreBundle\Doctrine\ORM\OrderRepository as BaseOrderRepository;
use Webgriffe\SyliusActiveCampaignPlugin\Doctrine\ORM\ActiveCampaignOrderRepositoryTrait;
use Webgriffe\SyliusActiveCampaignPlugin\Repository\ActiveCampaignOrderRepositoryInterface;

final class OrderRepository extends BaseOrderRepository implements ActiveCampaignOrderRepositoryInterface
{
    use ActiveCampaignOrderRepositoryTrait;
}
