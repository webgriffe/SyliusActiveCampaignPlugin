<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ContactTag;

use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ResourceResponseInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\UpdateResourceResponseInterface;

/** @todo Remove me */
final class UpdateContactTagResponse implements UpdateResourceResponseInterface
{
    public function __construct(
        private ContactTagResponse $contactTagResponse
    ) {
    }

    public function getResourceResponse(): ResourceResponseInterface
    {
        return $this->contactTagResponse;
    }
}
