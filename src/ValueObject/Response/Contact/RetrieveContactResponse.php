<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Contact;

use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ResourceResponseInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\RetrieveResourceResponseInterface;

final class RetrieveContactResponse implements RetrieveResourceResponseInterface
{
    /** @param array<array-key, array{contact: string, list: string, status: string, id: string}> $contactLists */
    public function __construct(
        private ContactResponse $contact,
        private array $contactLists
    ) {
    }

    public function getResourceResponse(): ResourceResponseInterface
    {
        return $this->contact;
    }

    /** @return array<array-key, array{contact: string, list: string, status: string, id: string}> */
    public function getContactLists(): array
    {
        return $this->contactLists;
    }
}
