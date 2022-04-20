<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\Contact;

use Webgriffe\SyliusActiveCampaignPlugin\Message\Contact\ContactListsUpdater;

final class ContactListsUpdaterHandler
{
    public function __invoke(ContactListsUpdater $message): void
    {
    }
}
