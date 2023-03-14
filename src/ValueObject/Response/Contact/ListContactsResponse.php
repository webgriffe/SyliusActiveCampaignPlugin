<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Contact;

use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ListResourcesResponseInterface;

final class ListContactsResponse implements ListResourcesResponseInterface
{
    /** @param ContactResponse[] $contacts */
    public function __construct(
        private array $contacts,
    ) {
    }

    /** @return ContactResponse[] */
    public function getResourceResponseLists(): array
    {
        return $this->contacts;
    }
}
