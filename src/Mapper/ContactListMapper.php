<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Mapper;

use Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign\ContactListFactoryInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ContactListInterface;

final class ContactListMapper implements ContactListMapperInterface
{
    public function __construct(
        private ContactListFactoryInterface $contactListFactory,
    ) {
    }

    public function mapFromListContactStatusAndSourceId(int $listId, int $contactId, int $status, ?int $sourceId = null): ContactListInterface
    {
        $contactList = $this->contactListFactory->createNew($listId, $contactId, $status);
        $contactList->setSourceId($sourceId);

        return $contactList;
    }
}
