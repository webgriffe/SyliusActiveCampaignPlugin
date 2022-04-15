<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign;

use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ContactList;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ContactListInterface;

final class ContactListFactory implements ContactListFactoryInterface
{
    public function createNew(int $listId, int $contactId, int $status): ContactListInterface
    {
        return new ContactList($listId, $contactId, $status);
    }
}
