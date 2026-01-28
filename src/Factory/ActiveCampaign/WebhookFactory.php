<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign;

use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\WebhookInterface;

final class WebhookFactory extends AbstractFactory implements WebhookFactoryInterface
{
    #[\Override]
    public function createNewFromNameAndUrl(string $name, string $url): WebhookInterface
    {
        /** @var class-string<WebhookInterface> $class */
        $class = $this->targetClassFQCN;

        return new $class(
            $name,
            $url,
        );
    }
}
