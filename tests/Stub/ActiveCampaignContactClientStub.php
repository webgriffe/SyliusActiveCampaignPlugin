<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusActiveCampaignPlugin\Stub;

use RuntimeException;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignResourceClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ResourceInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Contact\ContactResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Contact\CreateContactResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Contact\ListContactsResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\CreateResourceResponseInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ListResourcesResponseInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\UpdateResourceResponseInterface;

final class ActiveCampaignContactClientStub implements ActiveCampaignResourceClientInterface
{
    public static int $activeCampaignResourceId = 1234;

    /** @var array<string, ContactResponse> */
    public static array $activeCampaignResources = [];

    public function create(ResourceInterface $resource): CreateResourceResponseInterface
    {
        return new CreateContactResponse(
            new ContactResponse(
                self::$activeCampaignResourceId
            )
        );
    }

    public function list(array $queryParams = []): ListResourcesResponseInterface
    {
        $contacts = [];
        $emailToSearch = null;
        if (array_key_exists('email', $queryParams)) {
            $emailToSearch = $queryParams['email'];
        }
        foreach (self::$activeCampaignResources as $email => $activeCampaignResource) {
            if ($emailToSearch === null || $emailToSearch === $email) {
                $contacts[] = $activeCampaignResource;
            }
        }

        return new ListContactsResponse($contacts);
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
