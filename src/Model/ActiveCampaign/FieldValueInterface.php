<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign;

interface FieldValueInterface
{
    public function getField(): string;

    public function setField(string $field): void;

    public function getValue(): string;

    public function setValue(string $value): void;
}
