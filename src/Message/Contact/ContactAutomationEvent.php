<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Message\Contact;

final class ContactAutomationEvent
{
    /** @param string|int $customerId */
    public function __construct(
        private mixed $customerId,
        private string $automationId,
        private array $payload,
    ) {
    }

    /** @return string|int */
    public function getCustomerId(): mixed
    {
        return $this->customerId;
    }

    public function getAutomationId(): string
    {
        return $this->automationId;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }
}
