<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusActiveCampaignPlugin\Stub;

use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignResourceClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ResourceInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Contact\CreateContactContactResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Contact\CreateContactResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Contact\UpdateContactContactResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Contact\UpdateContactResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\CreateResourceResponseInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\UpdateResourceResponseInterface;

final class ActiveCampaignResourceClientStub implements ActiveCampaignResourceClientInterface
{
    public int $activeCampaignResourceId = 1234;

    public function create(ResourceInterface $resource): CreateResourceResponseInterface
    {
        return new CreateContactResponse(
            [],
            new CreateContactContactResponse(
                $resource->getEmail(),
                (new \DateTimeImmutable('now'))->format('c'),
                (new \DateTimeImmutable('now'))->format('c'),
                '',
                [],
                $this->activeCampaignResourceId,
                ''
            )
        );
    }

    public function update(int $activeCampaignResourceId, ResourceInterface $resource): UpdateResourceResponseInterface
    {
        return new UpdateContactResponse([], new UpdateContactContactResponse(
            (new \DateTimeImmutable('now'))->format('c'),
            $resource->getEmail(),
            '',
            $resource->getFirstName(),
            $resource->getLastName(),
            '0',
            '',
            '0',
            '0',
            '0',
            '8309146b50af1ed5f9cb40c7465a0315',
            '',
            '',
            '0',
            '0',
            '0',
            '0',
            (new \DateTimeImmutable('now'))->format('c'),
            (new \DateTimeImmutable('now'))->format('Y-m-d H:i:s'),
            (new \DateTimeImmutable('now'))->format('Y-m-d H:i:s'),
            [
                'bounceLogs' => 'https://:account.api-us1.com/api/:version/contacts/113/bounceLogs'
            ],
            $activeCampaignResourceId,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null
        ));
    }

    public function remove(int $activeCampaignResourceId): void
    {
    }
}
