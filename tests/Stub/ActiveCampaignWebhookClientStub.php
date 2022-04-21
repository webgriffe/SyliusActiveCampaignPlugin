<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusActiveCampaignPlugin\Stub;

use RuntimeException;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignResourceClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ResourceInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\CreateResourceResponseInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ListResourcesResponseInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\RetrieveResourceResponseInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\UpdateResourceResponseInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Webhook\CreateWebhookResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Webhook\ListWebhooksResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Webhook\WebhookResponse;

final class ActiveCampaignWebhookClientStub implements ActiveCampaignResourceClientInterface
{
    public static int $activeCampaignResourceId = 1;

    /** @var array{listid: string, url: string, webhook: WebhookResponse} */
    public static array $activeCampaignResources = [];

    public function create(ResourceInterface $resource): CreateResourceResponseInterface
    {
        return new CreateWebhookResponse(
            new WebhookResponse(
                self::$activeCampaignResourceId
            )
        );
    }

    public function list(array $queryParams = []): ListResourcesResponseInterface
    {
        $webhooks = [];
        $urlToSearch = null;
        $listIdToSearch = null;
        if (array_key_exists('filters[url]', $queryParams)) {
            $urlToSearch = $queryParams['filters[url]'];
        }
        if (array_key_exists('filters[listid]', $queryParams)) {
            $listIdToSearch = $queryParams['filters[listid]'];
        }
        foreach (self::$activeCampaignResources as $activeCampaignResource) {
            if (($urlToSearch === null && $listIdToSearch === null) || ($urlToSearch === $activeCampaignResource['url'] && $listIdToSearch === $activeCampaignResource['listid'])) {
                $webhooks[] = $activeCampaignResource['webhook'];
            }
        }

        return new ListWebhooksResponse($webhooks);
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
