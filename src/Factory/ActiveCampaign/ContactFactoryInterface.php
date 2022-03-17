<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign;

use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ContactInterface;

interface ContactFactoryInterface
{
    public function createNewFromEmail(string $email): ContactInterface;
}
