<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Factory;

use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignContactInterface;

interface ActiveCampaignContactFactoryInterface
{
    public function createNewFromEmail(string $email): ActiveCampaignContactInterface;
}
