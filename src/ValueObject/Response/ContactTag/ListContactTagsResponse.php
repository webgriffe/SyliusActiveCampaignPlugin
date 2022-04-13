<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ContactTag;

use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ListResourcesResponseInterface;

/** @todo Remove me */
final class ListContactTagsResponse implements ListResourcesResponseInterface
{
    /** @param ContactTagResponse[] $contactTags */
    public function __construct(
        private array $contactTags
    ) {
    }

    /** @return ContactTagResponse[] */
    public function getResourceResponseLists(): array
    {
        return $this->contactTags;
    }
}
