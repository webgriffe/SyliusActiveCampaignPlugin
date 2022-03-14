<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Model;

use Doctrine\ORM\Mapping as ORM;

trait ActiveCampaignAwareTrait
{
    /** @ORM\Column(name="active_campaign_id", type="integer", nullable=true) */
    protected ?int $activeCampaignId = null;

    public function getActiveCampaignId(): ?int
    {
        return $this->activeCampaignId;
    }

    public function setActiveCampaignId(?int $activeCampaignId): void
    {
        $this->activeCampaignId = $activeCampaignId;
    }
}
