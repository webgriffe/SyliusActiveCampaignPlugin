<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusActiveCampaignPlugin\Entity\Order;

use Sylius\Component\Core\Model\OrderInterface as BaseOrderInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface;

interface OrderInterface extends BaseOrderInterface, ActiveCampaignAwareInterface
{
}
