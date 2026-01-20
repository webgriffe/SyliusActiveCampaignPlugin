<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Webhook;

use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ResourceResponseInterface;

/** @psalm-api */
final class WebhookResponse implements ResourceResponseInterface
{
    public function __construct(
        private int $id,
    ) {
    }

    #[\Override]
    public function getId(): int
    {
        return $this->id;
    }
}
