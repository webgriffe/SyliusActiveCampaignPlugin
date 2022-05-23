<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign;

use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ContactInterface;

final class ContactFactory extends AbstractFactory implements ContactFactoryInterface
{
    public function createNewFromEmail(string $email): ContactInterface
    {
        /** @var ContactInterface $contact */
        $contact = new $this->targetClassFQCN($email);

        return $contact;
    }
}
