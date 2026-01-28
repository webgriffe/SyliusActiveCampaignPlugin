<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign;

use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\TagInterface;

final class TagFactory extends AbstractFactory implements TagFactoryInterface
{
    #[\Override]
    public function createNew(string $tagName, string $tagType = TagInterface::CONTACT_TAG_TYPE): TagInterface
    {
        /** @var class-string<TagInterface> $class */
        $class = $this->targetClassFQCN;

        return new $class(
            $tagName,
            $tagType,
        );
    }
}
