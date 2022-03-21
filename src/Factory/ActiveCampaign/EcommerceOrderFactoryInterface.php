<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign;

use DateTimeInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\EcommerceOrderInterface;

interface EcommerceOrderFactoryInterface
{
    public function createNew(string $email, string $connectionId, string $customerId, string $currency, int $totalPrice, DateTimeInterface $createdAt, ?string $externalId = null, ?string $externalCheckoutId = null, ?DateTimeInterface $abandonedDate = null): EcommerceOrderInterface;
}
