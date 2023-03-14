<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Tag;

use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ResourceResponseInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\UpdateResourceResponseInterface;

final class UpdateTagResponse implements UpdateResourceResponseInterface
{
    public function __construct(
        private TagResponse $tag,
    ) {
    }

    public function getResourceResponse(): ResourceResponseInterface
    {
        return $this->tag;
    }
}
