<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Doctrine\ORM;

use DateTimeInterface;
use Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Model\OrderInterface as BaseOrderInterface;

trait OrderActiveCampaignAwareRepositoryTrait
{
    /** @return OrderInterface[] */
    public function findNewCartsNotModifiedSince(DateTimeInterface $terminalDate): array
    {
        assert($this instanceof EntityRepository);

        return $this->createQueryBuilder('o')
            ->andWhere('o.state = :state')
            ->andWhere('o.activeCampaignId IS NULL')
            ->andWhere('o.updatedAt < :terminalDate')
            ->setParameter('state', BaseOrderInterface::STATE_CART)
            ->setParameter('terminalDate', $terminalDate)
            ->getQuery()
            ->getResult()
        ;
    }
}
