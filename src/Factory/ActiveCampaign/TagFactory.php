<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign;

use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\TagInterface;

final class TagFactory extends AbstractFactory implements TagFactoryInterface
{
    public function createNew(string $tagName, string $tagType = TagInterface::CONTACT_TAG_TYPE): TagInterface
    {
        /** @var TagInterface $tag */
        $tag = new $this->targetClassFQCN(
            $tagName,
            $tagType
        );

        return $tag;
    }
}
