<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\EcommerceCustomer;

use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignResourceClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Message\EcommerceCustomer\EcommerceCustomerRemove;

final class EcommerceCustomerRemoveHandler
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

    /**
     * @throws GuzzleException
     * @throws \Throwable
     * @throws \JsonException
     */
    public function __invoke(EcommerceCustomerRemove $message): void
    {
        try {
            $this->activeCampaignContactClient->remove($message->getActiveCampaignId());
        } catch (\Throwable $e) {
            $this->logger?->error($e->getMessage(), $e->getTrace());

            throw $e;
        }
    }
}
