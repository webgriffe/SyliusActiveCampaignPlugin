<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign;

use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ContactTagInterface;

final class ContactTagFactory extends AbstractFactory implements ContactTagFactoryInterface
{
    public function createNew(int $contactId, int $tagId): ContactTagInterface
    {
        /** @var ContactTagInterface $contactTag */
        $contactTag = new $this->targetClassFQCN($contactId, $tagId);

        return $contactTag;
    }
}
