<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Factory;

use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignContact;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignContactInterface;

final class ActiveCampaignContactFactory implements ActiveCampaignContactFactoryInterface
{
    public function createNewFromEmail(string $email): ActiveCampaignContactInterface
    {
        return new ActiveCampaignContact($email);
    }
}
