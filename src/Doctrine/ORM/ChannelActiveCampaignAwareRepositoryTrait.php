<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Doctrine\ORM;

use Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

trait ChannelActiveCampaignAwareRepositoryTrait
{
    /** @return ChannelInterface|null */
    public function findOneToEnqueue(mixed $id): ?ResourceInterface
    {
        assert($this instanceof EntityRepository);

        return $this->findOneBy([
            'id' => $id,
            'activeCampaignId' => null,
        ]);
    }

    /** @return ChannelInterface[] */
    public function findAllEnabledForActiveCampaign(): array
    {
        assert($this instanceof EntityRepository);

        return $this->createQueryBuilder('c')
            ->where('c.enabled = TRUE')
            ->getQuery()
            ->getResult()
        ;
    }

    /** @return ChannelInterface[] */
    public function findAllToEnqueue(): array
    {
        assert($this instanceof EntityRepository);

        return $this->findBy([
            'activeCampaignId' => null,
            'enabled' => true,
        ]);
    }
}
