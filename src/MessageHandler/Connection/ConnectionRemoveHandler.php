<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\Connection;

use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignResourceClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Connection\ConnectionRemove;

final class ConnectionRemoveHandler
{
    public function __construct(
        private ActiveCampaignResourceClientInterface $activeCampaignConnectionClient,
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

    /**
     * @throws \Throwable
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function __invoke(ConnectionRemove $message): void
    {
        try {
            $this->activeCampaignConnectionClient->remove($message->getActiveCampaignId());
        } catch (\Throwable $e) {
            $this->logger?->error($e->getMessage(), $e->getTrace());

            throw $e;
        }
    }
}
