<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign;

final class CreateContactResponse implements CreateContactResponseInterface
{
    /**
     * @param FieldValueInterface[] $fieldValues
     */
    public function __construct(
        private array $fieldValues,
        private ContactResponse $contact
    ) {
    }

    public function getFieldValues(): array
    {
        return $this->fieldValues;
    }

    public function getContact(): ContactResponse
    {
        return $this->contact;
    }
}
