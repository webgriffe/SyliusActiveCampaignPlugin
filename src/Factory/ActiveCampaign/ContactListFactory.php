<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign;

use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ContactListInterface;

final class ContactListFactory extends AbstractFactory implements ContactListFactoryInterface
{
    #[\Override]
    public function createNew(int $listId, int $contactId, int $status): ContactListInterface
    {
        /** @var class-string<ContactListInterface> $class */
        $class = $this->targetClassFQCN;

        return new $class($listId, $contactId, $status);
    }
}
