<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign;

use DateTimeInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\EcommerceOrder;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\EcommerceOrderInterface;

final class EcommerceOrderFactory implements EcommerceOrderFactoryInterface
{
    public function createNew(string $email, int $connectionId, int $customerId, string $currency, int $totalPrice, DateTimeInterface $createdAt, ?string $externalId = null, ?string $externalCheckoutId = null, ?DateTimeInterface $abandonedDate = null): EcommerceOrderInterface
    {
        return new EcommerceOrder(
            $email,
            $connectionId,
            $customerId,
            $currency,
            $totalPrice,
            $createdAt,
            $externalId,
            $externalCheckoutId,
            $abandonedDate
        );
    }
}
