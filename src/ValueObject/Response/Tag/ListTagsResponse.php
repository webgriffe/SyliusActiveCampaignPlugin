<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Tag;

use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ListResourcesResponseInterface;

final class ListTagsResponse implements ListResourcesResponseInterface
{
    /** @param TagResponse[] $tags */
    public function __construct(
        private array $tags,
    ) {
    }

    /** @return TagResponse[] */
    public function getResourceResponseLists(): array
    {
        return $this->tags;
    }
}
