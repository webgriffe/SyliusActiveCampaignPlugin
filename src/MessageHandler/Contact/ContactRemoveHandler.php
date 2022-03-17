<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\Contact;

use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignResourceClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Contact\ContactRemove;

final class ContactRemoveHandler
{
    public function __construct(
        private ActiveCampaignResourceClientInterface $activeCampaignContactClient,
    ) {
    }

    public function __invoke(ContactRemove $message): void
    {
        $this->activeCampaignContactClient->remove($message->getActiveCampaignId());
    }
}
