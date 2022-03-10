<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Factory;

use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\Contact;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ContactInterface;

final class ActiveCampaignContactFactory implements ActiveCampaignContactFactoryInterface
{
    public function createNewFromEmail(string $email): ContactInterface
    {
        return new Contact($email);
    }
}
