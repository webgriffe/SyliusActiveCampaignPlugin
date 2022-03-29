<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Doctrine\ORM;

use DateTimeInterface;
use Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Model\OrderInterface as BaseOrderInterface;

trait ActiveCampaignOrderRepositoryTrait
{
    /** @return OrderInterface[] */
    public function findNewCartsNotModifiedSince(DateTimeInterface $terminalDate): array
    {
        assert($this instanceof EntityRepository);

        return $this->createQueryBuilder('o')
            ->andWhere('o.state = :state')
            ->andWhere('o.customer IS NOT NULL')
            ->andWhere('o.activeCampaignId IS NULL')
            ->andWhere('o.updatedAt < :terminalDate')
            ->setParameter('state', BaseOrderInterface::STATE_CART)
            ->setParameter('terminalDate', $terminalDate)
            ->getQuery()
            ->getResult()
        ;
    }

    /** @return OrderInterface|null */
    public function findOneToEnqueue(mixed $id): ?OrderInterface
    {
        assert($this instanceof EntityRepository);

        return $this->findOneBy([
            'id' => $id,
            'activeCampaignId' => null,
        ]);
    }

    /** @return OrderInterface[] */
    public function findAllToEnqueue(): array
    {
        assert($this instanceof EntityRepository);

        return $this->createQueryBuilder('o')
            ->andWhere('o.customer IS NOT NULL')
            ->andWhere('o.activeCampaignId IS NULL')
            ->getQuery()
            ->getResult()
        ;
    }
}
