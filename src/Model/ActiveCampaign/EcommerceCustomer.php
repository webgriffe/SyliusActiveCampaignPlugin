<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign;

/** @psalm-api */
final class EcommerceCustomer implements EcommerceCustomerInterface
{
    public function __construct(
        private string $email,
        private string $connectionId,
        private string $externalId,
        private ?string $acceptsMarketing = self::MARKETING_NOT_OPTED_IN,
    ) {
    }

    #[\Override]
    public function getEmail(): string
    {
        return $this->email;
    }

    #[\Override]
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    #[\Override]
    public function getConnectionId(): string
    {
        return $this->connectionId;
    }

    #[\Override]
    public function setConnectionId(string $connectionId): void
    {
        $this->connectionId = $connectionId;
    }

    #[\Override]
    public function getExternalId(): string
    {
        return $this->externalId;
    }

    #[\Override]
    public function setExternalId(string $externalId): void
    {
        $this->externalId = $externalId;
    }

    #[\Override]
    public function getAcceptsMarketing(): ?string
    {
        return $this->acceptsMarketing;
    }

    #[\Override]
    public function setAcceptsMarketing(?string $acceptsMarketing): void
    {
        $this->acceptsMarketing = $acceptsMarketing;
    }
}
