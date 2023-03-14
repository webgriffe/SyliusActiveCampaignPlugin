<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Contact;

use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\CreateResourceResponseInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ResourceResponseInterface;

final class CreateContactResponse implements CreateResourceResponseInterface
{
    public function __construct(
        private ContactResponse $contact,
    ) {
    }

    public function getResourceResponse(): ResourceResponseInterface
    {
        return $this->contact;
    }
}
