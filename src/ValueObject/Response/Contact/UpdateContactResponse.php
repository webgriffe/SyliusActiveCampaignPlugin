<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Contact;

use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ResourceResponseInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\UpdateResourceResponseInterface;

/** @psalm-api */
final class UpdateContactResponse implements UpdateResourceResponseInterface
{
    public function __construct(
        private ContactResponse $contact,
    ) {
    }

    #[\Override]
    public function getResourceResponse(): ResourceResponseInterface
    {
        return $this->contact;
    }
}
