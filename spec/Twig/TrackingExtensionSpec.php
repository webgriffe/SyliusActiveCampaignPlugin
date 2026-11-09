<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusActiveCampaignPlugin\Twig;

use PhpSpec\ObjectBehavior;
use Webgriffe\SyliusActiveCampaignPlugin\Twig\TrackingExtension;

class TrackingExtensionSpec extends ObjectBehavior
{
    public function let(): void
    {
        $this->beConstructedWith(false);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(TrackingExtension::class);
    }

    public function it_returns_the_configured_track_by_default_value(): void
    {
        $this->isTrackingByDefault()->shouldReturn(false);
    }
}
