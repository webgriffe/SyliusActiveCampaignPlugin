<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\EcommerceCustomer;

use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ResourceResponseInterface;

/** @psalm-api */
final class EcommerceCustomerResponse implements ResourceResponseInterface
{
    public function __construct(
        private int $id,
    ) {
    }

    #[\Override]
    public function getId(): int
    {
        return $this->id;
    }
}
