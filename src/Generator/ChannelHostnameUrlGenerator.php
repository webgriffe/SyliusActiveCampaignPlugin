<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Generator;

use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;
use Webmozart\Assert\Assert;

final class ChannelHostnameUrlGenerator implements ChannelHostnameUrlGeneratorInterface
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private string $scheme = 'https'
    ) {
    }

    public function generate(ChannelInterface $channel, string $routeName, array $parameters = []): string
    {
        $channelRequestContext = new RequestContext();
        $channelHost = $channel->getHostname();
        Assert::string($channelHost);
        $channelRequestContext->setHost($channelHost);
        $channelRequestContext->setScheme($this->scheme);
        $channelRequestContext->setParameter('_host', $channelHost);
        $this->urlGenerator->setContext($channelRequestContext);

        return $this->urlGenerator->generate($routeName, $parameters, UrlGeneratorInterface::ABSOLUTE_URL);
    }
}
