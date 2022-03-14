<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusActiveCampaignPlugin\Client;

use RuntimeException;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ContactInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\CreateContactResponse;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\CreateContactResponseInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\UpdateContactResponseInterface;

final class ActiveCampaignClientStub implements ActiveCampaignClientInterface
{
    public int $activeCampaignId = 1234;

    public function createContact(ContactInterface $contact): CreateContactResponseInterface
    {
        return new CreateContactResponse(
            [],
            $contact->getEmail(),
            (new \DateTimeImmutable('now'))->format('c'),
            (new \DateTimeImmutable('now'))->format('c'),
            '',
            [],
            $this->activeCampaignId,
            ''
        );
    }

    public function updateContact(int $activeCampaignContactId, ContactInterface $contact): UpdateContactResponseInterface
    {
        throw new RuntimeException('TODO');
    }
}
