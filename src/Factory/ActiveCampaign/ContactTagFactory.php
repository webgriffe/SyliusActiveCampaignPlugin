<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign;

use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ContactTagInterface;

final class ContactTagFactory implements ContactTagFactoryInterface
{
    public function __construct(
        private string $contactTagFQCN
    ) {
    }

    public function createNew(int $contactId, int $tagId): ContactTagInterface
    {
        /** @var ContactTagInterface $contactTag */
        $contactTag = new $this->contactTagFQCN($contactId, $tagId);

        return $contactTag;
    }
}
