<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign;

use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ContactInterface;

final class ContactFactory extends AbstractFactory implements ContactFactoryInterface
{
    #[\Override]
    public function createNewFromEmail(string $email): ContactInterface
    {
        /** @var class-string<ContactInterface> $class */
        $class = $this->targetClassFQCN;

        return new $class($email);
    }
}
