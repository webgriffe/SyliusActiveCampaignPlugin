<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Model;

use Doctrine\ORM\Mapping as ORM;

trait ActiveCampaignAwareTrait
{
    /**
     * @ORM\Column(name="active_campaign_id", type="string", nullable=true)
     */
    protected ?string $activeCampaignId = null;

    public function getActiveCampaignId(): ?string
    {
        return $this->activeCampaignId;
    }

    public function setActiveCampaignId(?string $activeCampaignId): void
    {
        $this->activeCampaignId = $activeCampaignId;
    }
}
