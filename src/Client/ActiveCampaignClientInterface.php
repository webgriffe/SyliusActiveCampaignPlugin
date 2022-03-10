<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Client;

use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ContactInterface;

interface ActiveCampaignClientInterface
{
    public function createContact(ContactInterface $contact): void;
}
