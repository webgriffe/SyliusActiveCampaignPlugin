<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign;

use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ContactTagInterface;

interface ContactTagFactoryInterface
{
    public function createNew(int $contactId, int $tagId): ContactTagInterface;
}
