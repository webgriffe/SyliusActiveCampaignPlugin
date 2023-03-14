<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusActiveCampaignPlugin\Stub;

use RuntimeException;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignResourceClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ResourceInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Connection\ConnectionResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Connection\CreateConnectionResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Connection\ListConnectionsResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\CreateResourceResponseInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ListResourcesResponseInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\RetrieveResourceResponseInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\UpdateResourceResponseInterface;

final class ActiveCampaignConnectionClientStub implements ActiveCampaignResourceClientInterface
{
    public static int $activeCampaignResourceId = 1;

    /** @var array{service: string, externalid: string, connection: ConnectionResponse} */
    public static array $activeCampaignResources = [];

    public function create(ResourceInterface $resource): CreateResourceResponseInterface
    {
        return new CreateConnectionResponse(
            new ConnectionResponse(
                self::$activeCampaignResourceId,
            ),
        );
    }

    public function list(array $queryParams = []): ListResourcesResponseInterface
    {
        $connections = [];
        $serviceToSearch = null;
        $externalIdToSearch = null;
        if (array_key_exists('filters[service]', $queryParams)) {
            $serviceToSearch = $queryParams['filters[service]'];
        }
        if (array_key_exists('filters[externalid]', $queryParams)) {
            $externalIdToSearch = $queryParams['filters[externalid]'];
        }
        foreach (self::$activeCampaignResources as $activeCampaignResource) {
            if (($serviceToSearch === null && $externalIdToSearch === null) || ($serviceToSearch === $activeCampaignResource['service'] && $externalIdToSearch === $activeCampaignResource['externalid'])) {
                $connections[] = $activeCampaignResource['connection'];
            }
        }

        return new ListConnectionsResponse($connections);
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
