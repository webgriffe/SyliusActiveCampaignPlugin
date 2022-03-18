<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign;

interface EcommerceOrderProductInterface
{
    public function getName(): string;

    public function setName(string $name): void;

    public function getPrice(): int;

    public function setPrice(int $price): void;

    public function getQuantity(): int;

    public function setQuantity(int $quantity): void;

    public function getExternalId(): string;

    public function setExternalId(string $externalId): void;

    public function getCategory(): ?string;

    public function setCategory(?string $category): void;

    public function getSku(): ?string;

    public function setSku(?string $sku): void;

    public function getDescription(): ?string;

    public function setDescription(?string $description): void;

    public function getImageUrl(): ?string;

    public function setImageUrl(?string $imageUrl): void;

    public function getProductUrl(): ?string;

    public function setProductUrl(?string $productUrl): void;
}
