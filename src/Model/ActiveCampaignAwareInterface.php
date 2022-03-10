<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Model;

interface ActiveCampaignAwareInterface
{
    public function getActiveCampaignId(): ?string;

    public function setActiveCampaignId(?string $id): void;
}
