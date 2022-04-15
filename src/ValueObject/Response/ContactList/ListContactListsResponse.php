<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ContactList;

use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ListResourcesResponseInterface;

/** @todo Remove me */
final class ListContactListsResponse implements ListResourcesResponseInterface
{
    /** @param ContactListResponse[] $contactLists */
    public function __construct(
        private array $contactLists
    ) {
    }

    /** @return ContactListResponse[] */
    public function getResourceResponseLists(): array
    {
        return $this->contactLists;
    }
}
