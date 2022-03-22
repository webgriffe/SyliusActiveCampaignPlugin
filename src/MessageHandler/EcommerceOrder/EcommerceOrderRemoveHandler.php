<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\EcommerceOrder;

use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignResourceClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Message\EcommerceOrder\EcommerceOrderRemove;

final class EcommerceOrderRemoveHandler
{
    public function __construct(
        private ActiveCampaignResourceClientInterface $activeCampaignConnectionClient,
    ) {
    }

    public function __invoke(EcommerceOrderRemove $message): void
    {
        $this->activeCampaignConnectionClient->remove($message->getActiveCampaignId());
    }
}
