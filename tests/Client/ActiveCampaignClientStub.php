<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusActiveCampaignPlugin\Client;

use RuntimeException;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ContactInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ContactResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\CreateContactResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\UpdateContactResponse;

final class ActiveCampaignClientStub implements ActiveCampaignClientInterface
{
    public int $activeCampaignId = 1234;

    public function createContact(ContactInterface $contact): CreateContactResponse
    {
        return new CreateContactResponse(
            [],
            new ContactResponse(
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
        throw new RuntimeException('TODO');
    }

    public function removeContact(int $activeCampaignContactId): void
    {
        throw new RuntimeException('TODO');
    }
}
