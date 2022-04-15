<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ContactList;

use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ResourceResponseInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\UpdateResourceResponseInterface;

/** @todo Remove me */
final class UpdateContactListResponse implements UpdateResourceResponseInterface
{
    public function __construct(
        private ContactListResponse $contactList
    ) {
    }

    public function getResourceResponse(): ResourceResponseInterface
    {
        return $this->contactList;
    }
}
