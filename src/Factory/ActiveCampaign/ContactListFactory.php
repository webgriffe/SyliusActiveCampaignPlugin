<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign;

use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ContactListInterface;

final class ContactListFactory extends AbstractFactory implements ContactListFactoryInterface
{
    public function createNew(int $listId, int $contactId, int $status): ContactListInterface
    {
        /** @var ContactListInterface $contactList */
        $contactList = new $this->targetClassFQCN($listId, $contactId, $status);

        return $contactList;
    }
}
