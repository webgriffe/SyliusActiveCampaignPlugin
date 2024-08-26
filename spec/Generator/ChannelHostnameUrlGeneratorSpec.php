<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusActiveCampaignPlugin\Generator;

use Liip\ImagineBundle\Imagine\Cache\CacheManager;
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
        ChannelInterface $channel,
        CacheManager $cacheManager,
    ): void {
        $channel->getHostname()->willReturn('domain.com');

        $router->generate('route', [], UrlGeneratorInterface::ABSOLUTE_URL)->willReturn('/route');
        $cacheManager->getBrowserPath('image.jpg', 'filter')->willReturn('/image.jpg?filter=filter');

        $this->beConstructedWith($router, $cacheManager);
    }

    public function it_is_initializable(): void
    {
        $this->shouldBeAnInstanceOf(ChannelHostnameUrlGenerator::class);
    }

    public function it_implements_channel_hostname_url_generator_interface(): void
    {
        $this->shouldImplement(ChannelHostnameUrlGeneratorInterface::class);
    }

    public function it_should_returns_a_url_for_route(
        ChannelInterface $channel,
        UrlGeneratorInterface $router,
        RequestContext $context
    ): void {
        $router->getContext()->willReturn($context);
        $context->getHost()->willReturn('otherdomain.com');

        $context->setHost('domain.com')->shouldBeCalledOnce()->willReturn($context);
        $context->setHost('otherdomain.com')->shouldBeCalledOnce()->willReturn($context);

        $this->generateForRoute($channel, 'route', [])->shouldReturn('/route');
    }

    public function it_should_returns_a_url_for_image(
        ChannelInterface $channel,
        UrlGeneratorInterface $router,
        RequestContext $context
    ): void {
        $router->getContext()->willReturn($context);
        $context->getHost()->willReturn('otherdomain.com');

        $context->setHost('domain.com')->shouldBeCalledOnce()->willReturn($context);
        $context->setHost('otherdomain.com')->shouldBeCalledOnce()->willReturn($context);

        $this->generateForImage($channel, 'image.jpg', 'filter')->shouldReturn('/image.jpg?filter=filter');
    }
}
