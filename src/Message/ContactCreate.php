<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Message;

final class ContactCreate
{
    public function __construct(
        private int $customerId
    ) {
    }

    public function getCustomerId(): int
    {
        return $this->customerId;
    }
}
