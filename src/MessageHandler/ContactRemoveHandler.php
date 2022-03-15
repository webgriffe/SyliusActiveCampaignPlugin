<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\MessageHandler;

use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Message\ContactRemove;

final class ContactRemoveHandler
{
    public function __construct(
        private ActiveCampaignClientInterface $activeCampaignClient,
    ) {
    }

    public function __invoke(ContactRemove $message): void
    {
        $this->activeCampaignClient->removeContact($message->getActiveCampaignId());
    }
}
