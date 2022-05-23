<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign;

abstract class AbstractFactory
{
    public function __construct(
        protected string $targetClassFQCN
    ) {
    }
}
