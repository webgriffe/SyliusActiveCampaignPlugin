<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign;

/** @psalm-api */
final class FieldValue implements FieldValueInterface
{
    public function __construct(
        private string $field,
        private string $value,
    ) {
    }

    #[\Override]
    public function getField(): string
    {
        return $this->field;
    }

    #[\Override]
    public function setField(string $field): void
    {
        $this->field = $field;
    }

    #[\Override]
    public function getValue(): string
    {
        return $this->value;
    }

    #[\Override]
    public function setValue(string $value): void
    {
        $this->value = $value;
    }
}
