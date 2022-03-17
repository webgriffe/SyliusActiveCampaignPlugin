<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign;

interface EcommerceCustomerInterface extends ResourceInterface
{
    public function getEmail(): string;

    public function setEmail(string $email): void;

    public function getConnectionId(): string;

    public function setConnectionId(string $connectionId): void;

    public function getExternalId(): string;

    public function setExternalId(string $externalId): void;

    public function getAcceptsMarketing(): ?string;

    public function setAcceptsMarketing(?string $acceptsMarketing): void;
}
