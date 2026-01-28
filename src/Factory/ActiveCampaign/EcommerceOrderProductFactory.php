<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign;

use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\EcommerceOrderProductInterface;

final class EcommerceOrderProductFactory extends AbstractFactory implements EcommerceOrderProductFactoryInterface
{
    #[\Override]
    public function createNew(string $name, int $price, int $quantity, string $externalId): EcommerceOrderProductInterface
    {
        /** @var class-string<EcommerceOrderProductInterface> $class */
        $class = $this->targetClassFQCN;

        return new $class(
            $name,
            $price,
            $quantity,
            $externalId,
        );
    }
}
