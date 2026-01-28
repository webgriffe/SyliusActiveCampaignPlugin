<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign;

/** @psalm-api */
final class EcommerceOrderProduct implements EcommerceOrderProductInterface
{
    public function __construct(
        private string $name,
        private int $price,
        private int $quantity,
        private string $externalId,
        private ?string $category = null,
        private ?string $sku = null,
        private ?string $description = null,
        private ?string $imageUrl = null,
        private ?string $productUrl = null,
    ) {
    }

    #[\Override]
    public function getName(): string
    {
        return $this->name;
    }

    #[\Override]
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    #[\Override]
    public function getPrice(): int
    {
        return $this->price;
    }

    #[\Override]
    public function setPrice(int $price): void
    {
        $this->price = $price;
    }

    #[\Override]
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    #[\Override]
    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
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
    public function getCategory(): ?string
    {
        return $this->category;
    }

    #[\Override]
    public function setCategory(?string $category): void
    {
        $this->category = $category;
    }

    #[\Override]
    public function getSku(): ?string
    {
        return $this->sku;
    }

    #[\Override]
    public function setSku(?string $sku): void
    {
        $this->sku = $sku;
    }

    #[\Override]
    public function getDescription(): ?string
    {
        return $this->description;
    }

    #[\Override]
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    #[\Override]
    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    #[\Override]
    public function setImageUrl(?string $imageUrl): void
    {
        $this->imageUrl = $imageUrl;
    }

    #[\Override]
    public function getProductUrl(): ?string
    {
        return $this->productUrl;
    }

    #[\Override]
    public function setProductUrl(?string $productUrl): void
    {
        $this->productUrl = $productUrl;
    }
}
