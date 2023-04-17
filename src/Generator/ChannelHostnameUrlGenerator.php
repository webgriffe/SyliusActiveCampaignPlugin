<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Generator;

use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Webmozart\Assert\Assert;

final class ChannelHostnameUrlGenerator implements ChannelHostnameUrlGeneratorInterface
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public function generate(ChannelInterface $channel, string $routeName, array $parameters = []): string
    {
        $channelRequestContext = $this->urlGenerator->getContext();
        $previousHost = $channelRequestContext->getHost();
        $channelHost = $channel->getHostname();
        Assert::string($channelHost);
        $channelRequestContext->setHost($channelHost);

        $url = $this->urlGenerator->generate($routeName, $parameters, UrlGeneratorInterface::ABSOLUTE_URL);

        $channelRequestContext->setHost($previousHost);

        return $url;
    }
}
