<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\Connection;

use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignResourceClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Connection\ConnectionRemove;

final class ConnectionRemoveHandler
{
    public function __construct(
        private ActiveCampaignResourceClientInterface $activeCampaignConnectionClient,
    ) {
    }

    public function __invoke(ConnectionRemove $message): void
    {
        $this->activeCampaignConnectionClient->remove($message->getActiveCampaignId());
    }
}
