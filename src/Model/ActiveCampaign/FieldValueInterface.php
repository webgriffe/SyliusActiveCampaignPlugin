<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign;

interface FieldValueInterface
{
    public function getContact(): string;

    public function getField(): string;

    public function getValue(): string;

    public function getCreatedAt(): string;

    public function getUpdatedAt(): string;

    /** @return array<string, string> */
    public function getLinks(): array;

    public function getId(): string;

    public function getOwner(): string;
}
