<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign;

use DateTimeInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Exception\EcommerceOrderAbandonedDateRequiredException;
use Webgriffe\SyliusActiveCampaignPlugin\Exception\EcommerceOrderExternalIdNotValidException;

/** @psalm-api */
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
        private array $orderDiscounts = [],
    ) {
        if ($this->externalCheckoutId === null && $this->externalId === null) {
            throw new EcommerceOrderExternalIdNotValidException('One property between "externalId" and "externalCheckoutId" must be valued.');
        }
        if ($this->externalCheckoutId !== null && $this->abandonedDate === null) {
            throw new EcommerceOrderAbandonedDateRequiredException('The "abandonedDate" property can not be null if the "externalCheckoutId" is not null.');
        }
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
    public function getCustomerId(): string
    {
        return $this->customerId;
    }

    #[\Override]
    public function setCustomerId(string $customerId): void
    {
        $this->customerId = $customerId;
    }

    #[\Override]
    public function getCurrency(): string
    {
        return $this->currency;
    }

    #[\Override]
    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    #[\Override]
    public function getTotalPrice(): int
    {
        return $this->totalPrice;
    }

    #[\Override]
    public function setTotalPrice(int $totalPrice): void
    {
        $this->totalPrice = $totalPrice;
    }

    #[\Override]
    public function getExternalCreatedDate(): DateTimeInterface
    {
        return $this->externalCreatedDate;
    }

    #[\Override]
    public function setExternalCreatedDate(DateTimeInterface $externalCreatedDate): void
    {
        $this->externalCreatedDate = $externalCreatedDate;
    }

    #[\Override]
    public function getExternalId(): ?string
    {
        return $this->externalId;
    }

    #[\Override]
    public function setExternalId(?string $externalId): void
    {
        $this->externalId = $externalId;
    }

    #[\Override]
    public function getExternalCheckoutId(): ?string
    {
        return $this->externalCheckoutId;
    }

    #[\Override]
    public function setExternalCheckoutId(?string $externalCheckoutId): void
    {
        $this->externalCheckoutId = $externalCheckoutId;
    }

    #[\Override]
    public function getSource(): string
    {
        return $this->source;
    }

    #[\Override]
    public function setSource(string $source): void
    {
        $this->source = $source;
    }

    #[\Override]
    public function getOrderProducts(): array
    {
        return $this->orderProducts;
    }

    #[\Override]
    public function setOrderProducts(array $orderProducts): void
    {
        $this->orderProducts = $orderProducts;
    }

    #[\Override]
    public function getShippingAmount(): ?int
    {
        return $this->shippingAmount;
    }

    #[\Override]
    public function setShippingAmount(?int $shippingAmount): void
    {
        $this->shippingAmount = $shippingAmount;
    }

    #[\Override]
    public function getTaxAmount(): ?int
    {
        return $this->taxAmount;
    }

    #[\Override]
    public function setTaxAmount(?int $taxAmount): void
    {
        $this->taxAmount = $taxAmount;
    }

    #[\Override]
    public function getDiscountAmount(): ?int
    {
        return $this->discountAmount;
    }

    #[\Override]
    public function setDiscountAmount(?int $discountAmount): void
    {
        $this->discountAmount = $discountAmount;
    }

    #[\Override]
    public function getOrderUrl(): ?string
    {
        return $this->orderUrl;
    }

    #[\Override]
    public function setOrderUrl(?string $orderUrl): void
    {
        $this->orderUrl = $orderUrl;
    }

    #[\Override]
    public function getExternalUpdatedDate(): ?DateTimeInterface
    {
        return $this->externalUpdatedDate;
    }

    #[\Override]
    public function setExternalUpdatedDate(?DateTimeInterface $externalUpdatedDate): void
    {
        $this->externalUpdatedDate = $externalUpdatedDate;
    }

    #[\Override]
    public function getAbandonedDate(): ?DateTimeInterface
    {
        return $this->abandonedDate;
    }

    #[\Override]
    public function setAbandonedDate(?DateTimeInterface $abandonedDate): void
    {
        $this->abandonedDate = $abandonedDate;
    }

    #[\Override]
    public function getShippingMethod(): ?string
    {
        return $this->shippingMethod;
    }

    #[\Override]
    public function setShippingMethod(?string $shippingMethod): void
    {
        $this->shippingMethod = $shippingMethod;
    }

    #[\Override]
    public function getOrderNumber(): ?string
    {
        return $this->orderNumber;
    }

    #[\Override]
    public function setOrderNumber(?string $orderNumber): void
    {
        $this->orderNumber = $orderNumber;
    }

    #[\Override]
    public function getOrderDiscounts(): array
    {
        return $this->orderDiscounts;
    }

    #[\Override]
    public function setOrderDiscounts(array $orderDiscounts): void
    {
        $this->orderDiscounts = $orderDiscounts;
    }
}
