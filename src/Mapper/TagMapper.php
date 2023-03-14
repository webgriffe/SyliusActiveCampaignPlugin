<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Mapper;

use Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign\TagFactoryInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\TagInterface;

final class TagMapper implements TagMapperInterface
{
    public function __construct(
        private TagFactoryInterface $tagFactory,
    ) {
    }

    public function mapFromTagAndTagType(string $tag, string $tagType = TagInterface::CONTACT_TAG_TYPE): TagInterface
    {
        return $this->tagFactory->createNew($tag, $tagType);
    }
}
