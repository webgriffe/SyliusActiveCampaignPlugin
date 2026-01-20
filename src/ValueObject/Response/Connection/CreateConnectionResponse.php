<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Connection;

use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\CreateResourceResponseInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ResourceResponseInterface;

/** @psalm-api */
final class CreateConnectionResponse implements CreateResourceResponseInterface
{
    public function __construct(
        private ConnectionResponse $connection,
    ) {
    }

    #[\Override]
    public function getResourceResponse(): ResourceResponseInterface
    {
        return $this->connection;
    }
}
