<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusActiveCampaignPlugin\Stub;

use RuntimeException;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignResourceClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ResourceInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\CreateResourceResponseInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\EcommerceOrder\CreateEcommerceOrderResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\EcommerceOrder\EcommerceOrderResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\EcommerceOrder\ListEcommerceOrdersResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ListResourcesResponseInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\RetrieveResourceResponseInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\UpdateResourceResponseInterface;

final class ActiveCampaignEcommerceOrderClientStub implements ActiveCampaignResourceClientInterface
{
    public static int $activeCampaignResourceId = 222;

    /** @var array{connectionid: string, externalid: string, ecommerceOrder: EcommerceOrderResponse} */
    public static array $activeCampaignResources = [];

    public function create(ResourceInterface $resource): CreateResourceResponseInterface
    {
        return new CreateEcommerceOrderResponse(
            new EcommerceOrderResponse(
                self::$activeCampaignResourceId
            )
        );
    }

    public function list(array $queryParams = []): ListResourcesResponseInterface
    {
        $ecommerceOrders = [];
        $externalIdToSearch = null;
        $connectionToSearch = null;
        if (array_key_exists('filters[externalid]', $queryParams)) {
            $externalIdToSearch = $queryParams['filters[externalid]'];
        }
        if (array_key_exists('filters[connectionid]', $queryParams)) {
            $connectionToSearch = $queryParams['filters[connectionid]'];
        }
        foreach (self::$activeCampaignResources as $activeCampaignResource) {
            if (($externalIdToSearch === null && $connectionToSearch === null) || ($externalIdToSearch === $activeCampaignResource['externalid'] && $connectionToSearch === $activeCampaignResource['connectionid'])) {
                $ecommerceOrders[] = $activeCampaignResource['ecommerceOrder'];
            }
        }

        return new ListEcommerceOrdersResponse($ecommerceOrders);
    }

    public function update(int $activeCampaignResourceId, ResourceInterface $resource): UpdateResourceResponseInterface
    {
        throw new RuntimeException('Not implemented');
    }

    public function remove(int $activeCampaignResourceId): void
    {
        throw new RuntimeException('Not implemented');
    }

    public function get(int $resourceId): RetrieveResourceResponseInterface
    {
        throw new RuntimeException('Not implemented');
    }
}
