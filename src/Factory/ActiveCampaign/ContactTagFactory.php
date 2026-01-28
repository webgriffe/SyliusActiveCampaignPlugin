<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign;

use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ContactTagInterface;

final class ContactTagFactory extends AbstractFactory implements ContactTagFactoryInterface
{
    #[\Override]
    public function createNew(int $contactId, int $tagId): ContactTagInterface
    {
        /** @var class-string<ContactTagInterface> $class */
        $class = $this->targetClassFQCN;

        return new $class($contactId, $tagId);
    }
}
