<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusActiveCampaignPlugin\Client;

use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ContactInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ContactResponse;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ContactResponseInterface;

final class ActiveCampaignClientStub implements ActiveCampaignClientInterface
{
    public string $activeCampaignId = '1234';

    public function createContact(ContactInterface $contact): ContactResponseInterface
    {
        return new ContactResponse(
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
}
