<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Doctrine\ORM;

use Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\ChannelInterface;

trait ActiveCampaignChannelRepositoryTrait
{
    public function findOneToEnqueue(mixed $id): ?ChannelInterface
    {
        assert($this instanceof EntityRepository);

        return $this->findOneBy([
            'id' => $id,
        ]);
    }

    /** @return ChannelInterface[] */
    public function findAllToEnqueue(): array
    {
        assert($this instanceof EntityRepository);

        return $this->findBy([
            'enabled' => true,
        ]);
    }
}
