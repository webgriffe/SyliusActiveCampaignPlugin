<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Repository;

use DateTimeInterface;
use Sylius\Component\Core\Model\OrderInterface;

/**
 * @extends ActiveCampaignAwareRepositoryInterface<OrderInterface>
 */
interface OrderActiveCampaignAwareRepositoryInterface extends ActiveCampaignAwareRepositoryInterface
{
    /** @return OrderInterface[] */
    public function findNewCartsNotModifiedSince(DateTimeInterface $terminalDate): array;
}
