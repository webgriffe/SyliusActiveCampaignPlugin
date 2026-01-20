<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Model;

/** @psalm-api */
interface ChannelActiveCampaignAwareInterface extends ActiveCampaignAwareInterface
{
    public function getActiveCampaignListId(): ?int;

    public function setActiveCampaignListId(?int $activeCampaignListId): void;
}
