<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Mapper;

use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\TagInterface;

interface TagMapperInterface
{
    public function mapFromTagAndTagType(string $tag, string $tagType = TagInterface::CONTACT_TAG_TYPE): TagInterface;
}
