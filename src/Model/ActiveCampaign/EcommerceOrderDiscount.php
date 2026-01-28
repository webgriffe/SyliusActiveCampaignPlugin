<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign;

/** @psalm-api */
final class EcommerceOrderDiscount implements EcommerceOrderDiscountInterface
{
    public function __construct(
        private ?string $name = null,
        private ?string $type = self::ORDER_DISCOUNT_TYPE,
        private int $discountAmount = 0,
    ) {
    }

    #[\Override]
    public function getName(): ?string
    {
        return $this->name;
    }

    #[\Override]
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    #[\Override]
    public function getType(): ?string
    {
        return $this->type;
    }

    #[\Override]
    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    #[\Override]
    public function getDiscountAmount(): int
    {
        return $this->discountAmount;
    }

    #[\Override]
    public function setDiscountAmount(int $discountAmount): void
    {
        $this->discountAmount = $discountAmount;
    }
}
