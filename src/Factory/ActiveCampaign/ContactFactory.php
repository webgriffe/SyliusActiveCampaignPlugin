<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign;

use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\Contact;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ContactInterface;

final class ContactFactory implements ContactFactoryInterface
{
    public function createNewFromEmail(string $email): ContactInterface
    {
        return new Contact($email);
    }
}
