<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign;

final class Tag implements TagInterface
{
    public function __construct(
        private string $tag,
        private string $tagType = TagInterface::CONTACT_TAG_TYPE,
        private ?string $description = null,
    ) {
    }

    public function getTag(): string
    {
        return $this->tag;
    }

    public function setTag(string $tag): void
    {
        $this->tag = $tag;
    }

    public function getTagType(): string
    {
        return $this->tagType;
    }

    public function setTagType(string $tagType): void
    {
        $this->tagType = $tagType;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }
}
