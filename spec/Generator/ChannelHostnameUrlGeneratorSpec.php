<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusActiveCampaignPlugin\Generator;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;
use Webgriffe\SyliusActiveCampaignPlugin\Generator\ChannelHostnameUrlGenerator;
use Webgriffe\SyliusActiveCampaignPlugin\Generator\ChannelHostnameUrlGeneratorInterface;

class ChannelHostnameUrlGeneratorSpec extends ObjectBehavior
{
    public function let(
        UrlGeneratorInterface $router,
        ChannelInterface $channel
    ): void {
        $channel->getHostname()->willReturn('domain.com');

        $router->generate('route', [], UrlGeneratorInterface::ABSOLUTE_URL)->willReturn('/route');

        $this->beConstructedWith($router);
    }

    public function it_is_initializable(): void
    {
        $this->shouldBeAnInstanceOf(ChannelHostnameUrlGenerator::class);
    }

    public function it_implements_channel_hostname_url_generator_interface(): void
    {
        $this->shouldImplement(ChannelHostnameUrlGeneratorInterface::class);
    }

    public function it_should_returns_a_url(
        ChannelInterface $channel,
        UrlGeneratorInterface $router,
        RequestContext $context
    ): void {
        $router->getContext()->willReturn($context);
        $context->getHost()->willReturn('otherdomain.com');

        $context->setHost('domain.com')->shouldBeCalledOnce()->willReturn($context);
        $context->setHost('otherdomain.com')->shouldBeCalledOnce()->willReturn($context);

        $this->generate($channel, 'route', [])->shouldReturn('/route');
    }
}
