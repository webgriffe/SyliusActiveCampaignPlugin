<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ContactTag;

use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\CreateResourceResponseInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ResourceResponseInterface;

final class CreateContactTagResponse implements CreateResourceResponseInterface
{
    public function __construct(
        private ContactTagResponse $contactTag
    ) {
    }

    public function getResourceResponse(): ResourceResponseInterface
    {
        return $this->contactTag;
    }
}
