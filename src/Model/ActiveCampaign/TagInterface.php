<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign;

interface TagInterface extends ResourceInterface
{
    public const TEMPLATE_TAG_TYPE = 'template';

    public const CONTACT_TAG_TYPE = 'contact';

    public function getTag(): string;

    public function setTag(string $tag): void;

    public function getTagType(): string;

    public function setTagType(string $tagType): void;

    public function getDescription(): ?string;

    public function setDescription(?string $description): void;
}
