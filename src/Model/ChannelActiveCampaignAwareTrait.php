<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Model;

use Doctrine\ORM\Mapping as ORM;

trait ChannelActiveCampaignAwareTrait
{
    #[ORM\Column(name: 'active_campaign_list_id', type: 'integer', nullable: true)]
    protected ?int $activeCampaignListId = null;

    #[\Override]
    public function getActiveCampaignListId(): ?int
    {
        return $this->activeCampaignListId;
    }

    #[\Override]
    public function setActiveCampaignListId(?int $activeCampaignListId): void
    {
        $this->activeCampaignListId = $activeCampaignListId;
    }
}
