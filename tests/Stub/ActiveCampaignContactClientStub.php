<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusActiveCampaignPlugin\Stub;

use RuntimeException;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignResourceClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ResourceInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Contact\ContactResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Contact\CreateContactResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Contact\UpdateContactContactResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Contact\UpdateContactResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\CreateResourceResponseInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\UpdateResourceResponseInterface;

final class ActiveCampaignContactClientStub implements ActiveCampaignResourceClientInterface
{
    public int $activeCampaignResourceId = 1234;

    public function create(ResourceInterface $resource): CreateResourceResponseInterface
    {
        return new CreateContactResponse(
            new ContactResponse(
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
