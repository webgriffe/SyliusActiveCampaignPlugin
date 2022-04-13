<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign;

use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\Tag;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\TagInterface;

final class TagFactory implements TagFactoryInterface
{
    public function createNew(string $tag, string $tagType = TagInterface::CONTACT_TAG_TYPE): TagInterface
    {
        return new Tag(
            $tag,
            $tagType
        );
    }
}
