<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign;

final class EcommerceOrderDiscount implements EcommerceOrderDiscountInterface
{
    public const ORDER_DISCOUNT_TYPE = 'order';

    public const SHIPPING_DISCOUNT_TYPE = 'shipping';

    public function __construct(
        private ?string $name = null,
        private ?string $type = self::ORDER_DISCOUNT_TYPE,
        private ?int $discountAmount = null,
    ) {
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    public function getDiscountAmount(): ?int
    {
        return $this->discountAmount;
    }

    public function setDiscountAmount(?int $discountAmount): void
    {
        $this->discountAmount = $discountAmount;
    }
}
