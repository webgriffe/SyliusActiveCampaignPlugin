<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class TrackingExtension extends AbstractExtension
{
    public function __construct(
        private bool $trackByDefault,
    ) {
    }

    #[\Override]
    public function getFunctions(): array
    {
        return [
            new TwigFunction('webgriffe_active_campaign_is_tracking_by_default', [$this, 'isTrackingByDefault']),
        ];
    }

    public function isTrackingByDefault(): bool
    {
        return $this->trackByDefault;
    }
}
