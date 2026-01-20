<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign;

/** @psalm-api */
final class Tag implements TagInterface
{
    public function __construct(
        private string $tag,
        private string $tagType = TagInterface::CONTACT_TAG_TYPE,
        private ?string $description = null,
    ) {
    }

    #[\Override]
    public function getTag(): string
    {
        return $this->tag;
    }

    #[\Override]
    public function setTag(string $tag): void
    {
        $this->tag = $tag;
    }

    #[\Override]
    public function getTagType(): string
    {
        return $this->tagType;
    }

    #[\Override]
    public function setTagType(string $tagType): void
    {
        $this->tagType = $tagType;
    }

    #[\Override]
    public function getDescription(): ?string
    {
        return $this->description;
    }

    #[\Override]
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }
}
