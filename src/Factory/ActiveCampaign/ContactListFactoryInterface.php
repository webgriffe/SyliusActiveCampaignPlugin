<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign;

use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ContactListInterface;

interface ContactListFactoryInterface
{
    public function createNew(int $listId, int $contactId, int $status): ContactListInterface;
}
