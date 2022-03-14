<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Client;

use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ContactInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\CreateContactResponseInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\UpdateContactResponseInterface;

interface ActiveCampaignClientInterface
{
    public function createContact(ContactInterface $contact): CreateContactResponseInterface;

    public function updateContact(int $activeCampaignContactId, ContactInterface $contact): UpdateContactResponseInterface;
}
