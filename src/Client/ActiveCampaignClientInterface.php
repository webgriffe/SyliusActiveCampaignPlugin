<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Client;

use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignContactInterface;

interface ActiveCampaignClientInterface
{
    public function createContact(ActiveCampaignContactInterface $contact): void;
}
