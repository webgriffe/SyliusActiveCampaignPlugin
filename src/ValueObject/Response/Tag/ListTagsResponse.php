<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Tag;

use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ListResourcesResponseInterface;

/** @psalm-api */
final class ListTagsResponse implements ListResourcesResponseInterface
{
    /** @param TagResponse[] $tags */
    public function __construct(
        private array $tags,
    ) {
    }

    /** @return TagResponse[] */
    #[\Override]
    public function getResourceResponseLists(): array
    {
        return $this->tags;
    }
}
