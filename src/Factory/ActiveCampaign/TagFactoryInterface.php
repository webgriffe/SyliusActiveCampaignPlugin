<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign;

use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\TagInterface;

interface TagFactoryInterface
{
    public function createNew(string $tag, string $tagType = TagInterface::CONTACT_TAG_TYPE): TagInterface;
}
