<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign;

final class EcommerceCustomer implements EcommerceCustomerInterface
{
    public function __construct(
        private string $email,
        private string $connectionId,
        private string $externalId,
        private ?string $acceptsMarketing = null,
    ) {
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getConnectionId(): string
    {
        return $this->connectionId;
    }

    public function setConnectionId(string $connectionId): void
    {
        $this->connectionId = $connectionId;
    }

    public function getExternalId(): string
    {
        return $this->externalId;
    }

    public function setExternalId(string $externalId): void
    {
        $this->externalId = $externalId;
    }

    public function getAcceptsMarketing(): ?string
    {
        return $this->acceptsMarketing;
    }

    public function setAcceptsMarketing(?string $acceptsMarketing): void
    {
        $this->acceptsMarketing = $acceptsMarketing;
    }
}
