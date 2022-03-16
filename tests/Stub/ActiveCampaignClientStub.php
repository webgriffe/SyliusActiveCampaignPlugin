<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusActiveCampaignPlugin\Stub;

use RuntimeException;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ContactInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\CreateContactContactResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\CreateContactResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\UpdateContactContactResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\UpdateContactResponse;

final class ActiveCampaignClientStub implements ActiveCampaignClientInterface
{
    public int $activeCampaignId = 1234;

    public function createContact(ContactInterface $contact): CreateContactResponse
    {
        return new CreateContactResponse(
            [],
            new CreateContactContactResponse(
                $contact->getEmail(),
                (new \DateTimeImmutable('now'))->format('c'),
                (new \DateTimeImmutable('now'))->format('c'),
                '',
                [],
                $this->activeCampaignId,
                ''
            )
        );
    }

    public function updateContact(int $activeCampaignContactId, ContactInterface $contact): UpdateContactResponse
    {
        return new UpdateContactResponse([], new UpdateContactContactResponse(
            (new \DateTimeImmutable('now'))->format('c'),
            $contact->getEmail(),
            '',
            $contact->getFirstName(),
            $contact->getLastName(),
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
            $activeCampaignContactId,
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

    public function removeContact(int $activeCampaignContactId): void
    {
    }
}
