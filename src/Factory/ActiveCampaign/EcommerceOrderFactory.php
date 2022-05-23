<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign;

use DateTimeInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\EcommerceOrderInterface;

final class EcommerceOrderFactory extends AbstractFactory implements EcommerceOrderFactoryInterface
{
    public function createNew(string $email, string $connectionId, string $customerId, string $currency, int $totalPrice, DateTimeInterface $createdAt, ?string $externalId = null, ?string $externalCheckoutId = null, ?DateTimeInterface $abandonedDate = null): EcommerceOrderInterface
    {
        /** @var EcommerceOrderInterface $ecommerceOrder */
        $ecommerceOrder = new $this->targetClassFQCN(
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
