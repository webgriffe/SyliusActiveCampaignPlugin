<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Message\Contact;

final class ContactListsUpdater
{
    /** @param string|int $customerId */
    public function __construct(
        private mixed $customerId
    ) {
    }

    /** @return string|int */
    public function getCustomerId(): mixed
    {
        return $this->customerId;
    }
}
