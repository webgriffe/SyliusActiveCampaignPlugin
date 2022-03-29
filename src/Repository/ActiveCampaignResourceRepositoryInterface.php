<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Repository;

use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * @template T of ResourceInterface
 */
interface ActiveCampaignResourceRepositoryInterface
{
    /** @return T|null */
    public function findOneToEnqueue(mixed $id): ?ResourceInterface;

    /** @return T[] */
    public function findAllToEnqueue(): array;
}
