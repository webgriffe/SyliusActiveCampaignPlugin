<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign;

use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ContactTag;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ContactTagInterface;

final class ContactTagFactory implements ContactTagFactoryInterface
{
    public function createNew(int $contactId, int $tagId): ContactTagInterface
    {
        return new ContactTag($contactId, $tagId);
    }
}
