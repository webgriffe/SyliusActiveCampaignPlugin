<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\Contact;

use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Contact\ContactRemove;

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
