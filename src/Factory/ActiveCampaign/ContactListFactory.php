<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign;

use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ContactListInterface;

final class ContactListFactory implements ContactListFactoryInterface
{
    public function __construct(
        private string $contactListFQCN
    ) {
    }

    public function createNew(int $listId, int $contactId, int $status): ContactListInterface
    {
        /** @var ContactListInterface $contactList */
        $contactList = new $this->contactListFQCN($listId, $contactId, $status);

        return $contactList;
    }
}
