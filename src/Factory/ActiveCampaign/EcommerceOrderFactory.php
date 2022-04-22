<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign;

use DateTimeInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\EcommerceOrderInterface;

final class EcommerceOrderFactory implements EcommerceOrderFactoryInterface
{
    public function __construct(
        private string $ecommerceOrderFQCN
    ) {
    }

    public function createNew(string $email, string $connectionId, string $customerId, string $currency, int $totalPrice, DateTimeInterface $createdAt, ?string $externalId = null, ?string $externalCheckoutId = null, ?DateTimeInterface $abandonedDate = null): EcommerceOrderInterface
    {
        /** @var EcommerceOrderInterface $ecommerceOrder */
        $ecommerceOrder = new $this->ecommerceOrderFQCN(
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

        return $ecommerceOrder;
    }
}
