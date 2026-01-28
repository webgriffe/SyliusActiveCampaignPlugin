<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign;

use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\EcommerceCustomerInterface;

final class EcommerceCustomerFactory extends AbstractFactory implements EcommerceCustomerFactoryInterface
{
    #[\Override]
    public function createNew(string $email, string $connectionId, string $externalId): EcommerceCustomerInterface
    {
        /** @var class-string<EcommerceCustomerInterface> $class */
        $class = $this->targetClassFQCN;

        return new $class($email, $connectionId, $externalId);
    }
}
