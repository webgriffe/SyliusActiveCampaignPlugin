<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response;

final class CreateContactResponse
{
    /** @param FieldValueResponse[] $fieldValues */
    public function __construct(
        private array $fieldValues,
        private ContactResponse $contact
    ) {
    }

    /** @return FieldValueResponse[] */
    public function getFieldValues(): array
    {
        return $this->fieldValues;
    }

    public function getContact(): ContactResponse
    {
        return $this->contact;
    }
}
