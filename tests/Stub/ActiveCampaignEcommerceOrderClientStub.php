<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusActiveCampaignPlugin\Stub;

use RuntimeException;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignResourceClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ResourceInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\CreateResourceResponseInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\EcommerceOrder\EcommerceOrderResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\EcommerceOrder\CreateEcommerceOrderResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ListResourcesResponseInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\UpdateResourceResponseInterface;

final class ActiveCampaignEcommerceOrderClientStub implements ActiveCampaignResourceClientInterface
{
    public int $activeCampaignResourceId = 222;

    public function create(ResourceInterface $resource): CreateResourceResponseInterface
    {
        return new CreateEcommerceOrderResponse(
            new EcommerceOrderResponse(
                $this->activeCampaignResourceId
            )
        );
    }

    public function list(array $queryParams = []): ListResourcesResponseInterface
    {
        throw new RuntimeException('Not implemented');
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
