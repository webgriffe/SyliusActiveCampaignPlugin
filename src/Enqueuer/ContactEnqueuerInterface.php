<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Enqueuer;

use Sylius\Component\Core\Model\CustomerInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\CustomerActiveCampaignAwareInterface;

interface ContactEnqueuerInterface
{
    /** @param CustomerInterface&CustomerActiveCampaignAwareInterface $customer */
    public function enqueue($customer): void;
}
