<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign;

use DateTimeInterface;

interface EcommerceOrderInterface extends ResourceInterface
{
    public const HISTORICAL_SOURCE_CODE = '0';

    /** @docs Only real-time orders (source = 1) will show up on your Ecommerce Dashboard and trigger the “Makes a purchase” automation start, abandoned cart actions, customer conversions, or revenue attributions. */
    public const REAL_TIME_SOURCE_CODE = '1';

    public function getEmail(): string;

    public function setEmail(string $email): void;

    public function getConnectionId(): string;

    public function setConnectionId(string $connectionId): void;

    public function getCustomerId(): string;

    public function setCustomerId(string $customerId): void;

    public function getCurrency(): string;

    public function setCurrency(string $currency): void;

    public function getTotalPrice(): int;

    public function setTotalPrice(int $totalPrice): void;

    public function getExternalCreatedDate(): DateTimeInterface;

    public function setExternalCreatedDate(DateTimeInterface $externalCreatedDate): void;

    public function getExternalId(): ?string;

    public function setExternalId(?string $externalId): void;

    public function getExternalCheckoutId(): ?string;

    public function setExternalCheckoutId(?string $externalCheckoutId): void;

    public function getSource(): string;

    public function setSource(string $source): void;

    /** @return EcommerceOrderProductInterface[] */
    public function getOrderProducts(): array;

    /** @param EcommerceOrderProductInterface[] $orderProducts */
    public function setOrderProducts(array $orderProducts): void;

    public function getShippingAmount(): ?int;

    public function setShippingAmount(?int $shippingAmount): void;

    public function getTaxAmount(): ?int;

    public function setTaxAmount(?int $taxAmount): void;

    public function getDiscountAmount(): ?int;

    public function setDiscountAmount(?int $discountAmount): void;

    public function getOrderUrl(): ?string;

    public function setOrderUrl(?string $orderUrl): void;

    public function getExternalUpdatedDate(): ?DateTimeInterface;

    public function setExternalUpdatedDate(?DateTimeInterface $externalUpdatedDate): void;

    public function getAbandonedDate(): ?DateTimeInterface;

    public function setAbandonedDate(?DateTimeInterface $abandonedDate): void;

    public function getShippingMethod(): ?string;

    public function setShippingMethod(?string $shippingMethod): void;

    public function getOrderNumber(): ?string;

    public function setOrderNumber(?string $orderNumber): void;

    /** @return EcommerceOrderDiscountInterface[] */
    public function getOrderDiscounts(): array;

    /** @param EcommerceOrderDiscountInterface[] $orderDiscounts */
    public function setOrderDiscounts(array $orderDiscounts): void;
}
