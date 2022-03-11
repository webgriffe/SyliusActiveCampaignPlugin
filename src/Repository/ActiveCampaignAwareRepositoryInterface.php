<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Repository;

use Sylius\Component\Core\Model\CustomerInterface;

interface ActiveCampaignAwareRepositoryInterface
{
    public function findOneToEnqueue(mixed $id): ?CustomerInterface;

    /** @return CustomerInterface[] */
    public function findAllToEnqueue(): array;
}
