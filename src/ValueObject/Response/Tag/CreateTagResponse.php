<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Tag;

use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\CreateResourceResponseInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ResourceResponseInterface;

final class CreateTagResponse implements CreateResourceResponseInterface
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
