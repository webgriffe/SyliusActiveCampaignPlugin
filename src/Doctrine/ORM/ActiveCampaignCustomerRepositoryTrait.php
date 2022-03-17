<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Doctrine\ORM;

use Doctrine\ORM\EntityRepository;
use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * @template T of ResourceInterface
 */
trait ActiveCampaignCustomerRepositoryTrait
{
    /** @return T|null */
    public function findOneToEnqueue(mixed $id): ?ResourceInterface
    {
        assert($this instanceof EntityRepository);

        return $this->findOneBy([
            'id' => $id,
            'activeCampaignId' => null,
        ]);
    }

    /** @return T[] */
    public function findAllToEnqueue(): array
    {
        assert($this instanceof EntityRepository);

        return $this->findBy([
            'activeCampaignId' => null,
        ]);
    }
}
