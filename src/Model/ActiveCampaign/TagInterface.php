<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign;

/** @psalm-api */
interface TagInterface extends ResourceInterface
{
    public const string TEMPLATE_TAG_TYPE = 'template';

    public const string CONTACT_TAG_TYPE = 'contact';

    public function getTag(): string;

    public function setTag(string $tag): void;

    public function getTagType(): string;

    public function setTagType(string $tagType): void;

    public function getDescription(): ?string;

    public function setDescription(?string $description): void;
}
