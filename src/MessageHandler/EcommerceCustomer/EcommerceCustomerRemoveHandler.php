<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\EcommerceCustomer;

use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignResourceClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Message\EcommerceCustomer\EcommerceCustomerRemove;

final class EcommerceCustomerRemoveHandler
{
    public function __construct(
        private ActiveCampaignResourceClientInterface $activeCampaignContactClient,
    ) {
    }

    public function __invoke(EcommerceCustomerRemove $message): void
    {
        $this->activeCampaignContactClient->remove($message->getActiveCampaignId());
    }
}
