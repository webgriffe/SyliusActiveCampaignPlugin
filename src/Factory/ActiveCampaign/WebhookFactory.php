<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign;

use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\WebhookInterface;

final class WebhookFactory extends AbstractFactory implements WebhookFactoryInterface
{
    public function createNewFromNameAndUrl(string $name, string $url): WebhookInterface
    {
        /** @var WebhookInterface $webhook */
        $webhook = new $this->targetClassFQCN(
            $name,
            $url
        );

        return $webhook;
    }
}
