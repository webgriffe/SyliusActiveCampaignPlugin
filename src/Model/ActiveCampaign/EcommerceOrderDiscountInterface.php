<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign;

interface EcommerceOrderDiscountInterface
{
    public function getName(): ?string;

    public function setName(?string $name): void;

    public function getType(): ?string;

    public function setType(?string $type): void;

    public function getDiscountAmount(): ?int;

    public function setDiscountAmount(?int $discountAmount): void;
}
