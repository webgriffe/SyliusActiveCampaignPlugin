<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\EcommerceOrder;

use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignResourceClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Message\EcommerceOrder\EcommerceOrderRemove;

final class EcommerceOrderRemoveHandler
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
     * @throws GuzzleException
     * @throws \Throwable
     * @throws \JsonException
     */
    public function __invoke(EcommerceOrderRemove $message): void
    {
        try {
            $this->activeCampaignConnectionClient->remove($message->getActiveCampaignId());
        } catch (\Throwable $e) {
            $this->logger?->error($e->getMessage(), $e->getTrace());

            throw $e;
        }
    }
}
