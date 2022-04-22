<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign;

use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ContactInterface;

final class ContactFactory implements ContactFactoryInterface
{
    public function __construct(
        private string $contactFQCN
    ) {
    }

    public function createNewFromEmail(string $email): ContactInterface
    {
        /** @var ContactInterface $contact */
        $contact = new $this->contactFQCN($email);

        return $contact;
    }
}
