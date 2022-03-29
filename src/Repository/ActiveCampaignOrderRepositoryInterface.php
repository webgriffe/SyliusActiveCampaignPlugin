<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Repository;

use DateTimeInterface;
use Sylius\Component\Core\Model\OrderInterface;

/**
 * @extends ActiveCampaignResourceRepositoryInterface<OrderInterface>
 */
interface ActiveCampaignOrderRepositoryInterface extends ActiveCampaignResourceRepositoryInterface
{
    /** @return OrderInterface[] */
    public function findNewCartsNotModifiedSince(DateTimeInterface $terminalDate): array;
}
