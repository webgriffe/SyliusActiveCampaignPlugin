<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Contact;

use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ResourceResponseInterface;

final class RetrieveContactResponse implements RetrieveContactResponseInterface
{
    /** @var array<array-key, array{contact: string, list: string, status: string, id: string}> */
    private array $contactLists = [];

    /** @param array<array-key, array> $contactLists */
    public function __construct(
        private ContactResponse $contact,
        array $contactLists
    ) {
        // @TODO This should be replaced by serializing this array of array as an array of objects
        /** @var array{contact: string, list: string, status: string, id: string}|array<string, string> $contactList */
        foreach ($contactLists as $contactList) {
            if (!array_key_exists('contact', $contactList) ||
                !array_key_exists('list', $contactList) ||
                !array_key_exists('status', $contactList) ||
                !array_key_exists('id', $contactList)
            ) {
                continue;
            }
            $this->contactLists[] = $contactList;
        }
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
