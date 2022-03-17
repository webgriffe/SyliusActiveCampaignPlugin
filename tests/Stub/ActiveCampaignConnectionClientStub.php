<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusActiveCampaignPlugin\Stub;

use RuntimeException;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignResourceClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ResourceInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Connection\CreateConnectionConnectionResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Connection\CreateConnectionResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\CreateResourceResponseInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\UpdateResourceResponseInterface;

final class ActiveCampaignConnectionClientStub implements ActiveCampaignResourceClientInterface
{
    public int $activeCampaignResourceId = 1;

    public function create(ResourceInterface $resource): CreateResourceResponseInterface
    {
        return new CreateConnectionResponse(
            new CreateConnectionConnectionResponse(
                0,
                'sylius',
                'sylius',
                'sylius',
                '',
                '',
                '',
                '',
                [],
                $this->activeCampaignResourceId,
            )
        );
    }

    public function update(int $activeCampaignResourceId, ResourceInterface $resource): UpdateResourceResponseInterface
    {
        throw new RuntimeException('Not implemented');
    }

    public function remove(int $activeCampaignResourceId): void
    {
    }
}
