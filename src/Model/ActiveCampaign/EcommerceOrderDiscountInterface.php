<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign;

/** @psalm-api */
interface EcommerceOrderDiscountInterface
{
    public const string ORDER_DISCOUNT_TYPE = 'order';

    public const string SHIPPING_DISCOUNT_TYPE = 'shipping';

    public function getName(): ?string;

    public function setName(?string $name): void;

    public function getType(): ?string;

    public function setType(?string $type): void;

    public function getDiscountAmount(): int;

    public function setDiscountAmount(int $discountAmount): void;
}
