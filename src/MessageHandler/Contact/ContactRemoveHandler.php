<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\Contact;

use Psr\Log\LoggerInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignResourceClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Contact\ContactRemove;

final class ContactRemoveHandler
{
    public function __construct(
        private ActiveCampaignResourceClientInterface $activeCampaignContactClient,
        private ?LoggerInterface $logger = null,
    ) {
        if ($this->logger === null) {
            trigger_deprecation(
                'webgriffe/sylius-active-campaign-plugin',
                'v0.12.2',
                'The logger argument is mandatory.',
            );
        }
    }

    public function __invoke(ContactRemove $message): void
    {
        try {
            $this->activeCampaignContactClient->remove($message->getActiveCampaignId());
        } catch (\Throwable $e) {
            $this->logger?->error($e->getMessage(), $e->getTrace());
        }
    }
}
