<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ContactList;

use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\CreateResourceResponseInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ResourceResponseInterface;

final class CreateContactListResponse implements CreateResourceResponseInterface
{
    public function __construct(
        private ContactListResponse $contactList,
    ) {
    }

    public function getResourceResponse(): ResourceResponseInterface
    {
        return $this->contactList;
    }
}
