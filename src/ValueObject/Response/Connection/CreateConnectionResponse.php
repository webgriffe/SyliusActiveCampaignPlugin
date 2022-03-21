<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Connection;

use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\CreateResourceResponseInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ResourceResponseInterface;

final class CreateConnectionResponse implements CreateResourceResponseInterface
{
    public function __construct(
        private ConnectionResponse $connection
    ) {
    }

    public function getResourceResponse(): ResourceResponseInterface
    {
        return $this->connection;
    }
}
