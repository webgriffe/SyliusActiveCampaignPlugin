<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Connection;

use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ResourceResponseInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\UpdateResourceResponseInterface;

final class UpdateConnectionResponse implements UpdateResourceResponseInterface
{
    public function __construct(
        private UpdateConnectionConnectionResponse $connection
    ) {
    }

    public function getConnection(): UpdateConnectionConnectionResponse
    {
        return $this->connection;
    }

    public function getResourceResponse(): ResourceResponseInterface
    {
        return $this->connection;
    }
}
