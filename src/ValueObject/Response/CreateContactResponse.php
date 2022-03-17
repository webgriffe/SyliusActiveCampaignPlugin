<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response;

final class CreateContactResponse implements CreateResourceResponseInterface
{
    /** @param FieldValueResponse[] $fieldValues */
    public function __construct(
        private array $fieldValues,
        private CreateContactContactResponse $contact
    ) {
    }

    /** @return FieldValueResponse[] */
    public function getFieldValues(): array
    {
        return $this->fieldValues;
    }

    public function getContact(): CreateContactContactResponse
    {
        return $this->contact;
    }
}
