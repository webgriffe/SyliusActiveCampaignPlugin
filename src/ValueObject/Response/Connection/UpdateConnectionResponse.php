<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Connection;

use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ResourceResponseInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\UpdateResourceResponseInterface;

/** @psalm-api */
final class UpdateConnectionResponse implements UpdateResourceResponseInterface
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
