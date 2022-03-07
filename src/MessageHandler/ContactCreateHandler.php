<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\MessageHandler;

use Webgriffe\SyliusActiveCampaignPlugin\Message\ContactCreate;

final class ContactCreateHandler
{
    public function __invoke(ContactCreate $message): void
    {
    }
}
