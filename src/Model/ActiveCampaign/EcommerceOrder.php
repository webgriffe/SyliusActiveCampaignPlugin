<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign;

use DateTimeInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Exception\EcommerceOrderAbandonedDateRequiredException;
use Webgriffe\SyliusActiveCampaignPlugin\Exception\EcommerceOrderExternalIdNotValidException;

final class EcommerceOrder implements EcommerceOrderInterface
{
    /**
     * @param EcommerceOrderProductInterface[] $orderProducts
     * @param EcommerceOrderDiscountInterface[] $orderDiscounts
     */
    public function __construct(
        private string $email,
        private string $connectionId,
        private string $customerId,
        private string $currency,
        private int $totalPrice,
        private DateTimeInterface $externalCreatedDate,
        private ?string $externalId = null,
        private ?string $externalCheckoutId = null,
        private ?DateTimeInterface $abandonedDate = null,
        private string $source = self::REAL_TIME_SOURCE_CODE,
        private array $orderProducts = [],
        private ?int $shippingAmount = null,
        private ?int $taxAmount = null,
        private ?int $discountAmount = null,
        private ?string $orderUrl = null,
        private ?DateTimeInterface $externalUpdatedDate = null,
        private ?string $shippingMethod = null,
        private ?string $orderNumber = null,
        private array $orderDiscounts = []
    ) {
        if ($this->externalCheckoutId === null && $this->externalId === null) {
            throw new EcommerceOrderExternalIdNotValidException('One property between "externalId" and "externalCheckoutId" must be valued.');
        }
        if ($this->externalCheckoutId !== null && $this->abandonedDate === null) {
            throw new EcommerceOrderAbandonedDateRequiredException('The "abandonedDate" property can not be null if the "externalCheckoutId" is not null.');
        }
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

    public function getCustomerId(): string
    {
        return $this->customerId;
    }

    public function setCustomerId(string $customerId): void
    {
        $this->customerId = $customerId;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    public function getTotalPrice(): int
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(int $totalPrice): void
    {
        $this->totalPrice = $totalPrice;
    }

    public function getExternalCreatedDate(): DateTimeInterface
    {
        return $this->externalCreatedDate;
    }

    public function setExternalCreatedDate(DateTimeInterface $externalCreatedDate): void
    {
        $this->externalCreatedDate = $externalCreatedDate;
    }

    public function getExternalId(): ?string
    {
        return $this->externalId;
    }

    public function setExternalId(?string $externalId): void
    {
        $this->externalId = $externalId;
    }

    public function getExternalCheckoutId(): ?string
    {
        return $this->externalCheckoutId;
    }

    public function setExternalCheckoutId(?string $externalCheckoutId): void
    {
        $this->externalCheckoutId = $externalCheckoutId;
    }

    public function getSource(): string
    {
        return $this->source;
    }

    public function setSource(string $source): void
    {
        $this->source = $source;
    }

    public function getOrderProducts(): array
    {
        return $this->orderProducts;
    }

    public function setOrderProducts(array $orderProducts): void
    {
        $this->orderProducts = $orderProducts;
    }

    public function getShippingAmount(): ?int
    {
        return $this->shippingAmount;
    }

    public function setShippingAmount(?int $shippingAmount): void
    {
        $this->shippingAmount = $shippingAmount;
    }

    public function getTaxAmount(): ?int
    {
        return $this->taxAmount;
    }

    public function setTaxAmount(?int $taxAmount): void
    {
        $this->taxAmount = $taxAmount;
    }

    public function getDiscountAmount(): ?int
    {
        return $this->discountAmount;
    }

    public function setDiscountAmount(?int $discountAmount): void
    {
        $this->discountAmount = $discountAmount;
    }

    public function getOrderUrl(): ?string
    {
        return $this->orderUrl;
    }

    public function setOrderUrl(?string $orderUrl): void
    {
        $this->orderUrl = $orderUrl;
    }

    public function getExternalUpdatedDate(): ?DateTimeInterface
    {
        return $this->externalUpdatedDate;
    }

    public function setExternalUpdatedDate(?DateTimeInterface $externalUpdatedDate): void
    {
        $this->externalUpdatedDate = $externalUpdatedDate;
    }

    public function getAbandonedDate(): ?DateTimeInterface
    {
        return $this->abandonedDate;
    }

    public function setAbandonedDate(?DateTimeInterface $abandonedDate): void
    {
        $this->abandonedDate = $abandonedDate;
    }

    public function getShippingMethod(): ?string
    {
        return $this->shippingMethod;
    }

    public function setShippingMethod(?string $shippingMethod): void
    {
        $this->shippingMethod = $shippingMethod;
    }

    public function getOrderNumber(): ?string
    {
        return $this->orderNumber;
    }

    public function setOrderNumber(?string $orderNumber): void
    {
        $this->orderNumber = $orderNumber;
    }

    public function getOrderDiscounts(): array
    {
        return $this->orderDiscounts;
    }

    public function setOrderDiscounts(array $orderDiscounts): void
    {
        $this->orderDiscounts = $orderDiscounts;
    }
}
