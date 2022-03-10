<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaignContact;

use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\CreateContactResponseInterface;

interface CreateContactResponseFactoryInterface
{
    public function createNewFromPayload(array $payload): CreateContactResponseInterface;
}
