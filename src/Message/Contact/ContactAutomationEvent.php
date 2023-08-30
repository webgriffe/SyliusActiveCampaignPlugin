<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Message\Contact;

final class ContactAutomationEvent
{
    /** @param string|int $customerId */
    public function __construct(
        private mixed $customerId,
        private string $automationId,
    ) {
    }

    public function getAutomationId(): string
    {
        return $this->automationId;
    }

    /** @return string|int */
    public function getCustomerId(): mixed
    {
        return $this->customerId;
    }
}
