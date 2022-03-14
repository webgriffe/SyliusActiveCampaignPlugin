<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Model;

interface ActiveCampaignAwareInterface
{
    public function getActiveCampaignId(): ?int;

    public function setActiveCampaignId(?int $id): void;
}
