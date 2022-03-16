<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response;

final class UpdateContactResponse
{
    /**
     * @param FieldValueResponse[] $fieldValues
     */
    public function __construct(
        private array $fieldValues,
        private UpdateContactContactResponse $contact
    ) {
    }

    /** @return FieldValueResponse[] */
    public function getFieldValues(): array
    {
        return $this->fieldValues;
    }

    public function getContact(): UpdateContactContactResponse
    {
        return $this->contact;
    }
}
