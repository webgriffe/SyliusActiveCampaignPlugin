<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Contact;

use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\CreateResourceResponseInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ResourceResponseInterface;

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

    public function getResourceResponse(): ResourceResponseInterface
    {
        return $this->contact;
    }
}
