<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Message\Contact;

final class ContactRemove
{
    public function __construct(
        private int $activeCampaignId
    ) {
    }

    public function getActiveCampaignId(): int
    {
        return $this->activeCampaignId;
    }
}
