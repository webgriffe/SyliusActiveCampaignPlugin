<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusActiveCampaignPlugin\Stub;

use RuntimeException;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignResourceClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ResourceInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\CreateResourceResponseInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\EcommerceCustomer\EcommerceCustomerResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\EcommerceCustomer\CreateEcommerceCustomerResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\UpdateResourceResponseInterface;

final class ActiveCampaignEcommerceCustomerClientStub implements ActiveCampaignResourceClientInterface
{
    public int $activeCampaignResourceId = 3423;

    public function create(ResourceInterface $resource): CreateResourceResponseInterface
    {
        return new CreateEcommerceCustomerResponse(
            new EcommerceCustomerResponse(
                $this->activeCampaignResourceId
            )
        );
    }

    public function update(int $activeCampaignResourceId, ResourceInterface $resource): UpdateResourceResponseInterface
    {
        throw new RuntimeException('Not implemented');
    }

    public function remove(int $activeCampaignResourceId): void
    {
        throw new RuntimeException('Not implemented');
    }
}
