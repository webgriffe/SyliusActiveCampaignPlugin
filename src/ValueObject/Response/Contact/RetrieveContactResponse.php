<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Contact;

use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ResourceResponseInterface;

final class RetrieveContactResponse implements RetrieveContactResponseInterface
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

    public function getContactLists(): array
    {
        return $this->contactLists;
    }
}
