<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Doctrine\ORM;

use Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\CustomerInterface;

trait ActiveCampaignCustomerRepositoryTrait
{
    public function findOneToEnqueue(mixed $id): ?CustomerInterface
    {
        assert($this instanceof EntityRepository);

        return $this->findOneBy([
            'id' => $id,
            'activeCampaignId' => null,
        ]);
    }

    /** @return CustomerInterface[] */
    public function findAllToEnqueue(): array
    {
        assert($this instanceof EntityRepository);

        return $this->findBy([
            'activeCampaignId' => null,
        ]);
    }
}
