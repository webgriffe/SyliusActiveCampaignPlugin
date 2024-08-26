<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Generator;

use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\Asset\UrlPackage;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Webmozart\Assert\Assert;

final class ChannelHostnameUrlGenerator implements ChannelHostnameUrlGeneratorInterface
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private CacheManager $cacheManager,
    ) {
    }

    public function generateForRoute(ChannelInterface $channel, string $routeName, array $parameters = []): string
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

    public function generateForImage(ChannelInterface $channel, string $imagePath, ?string $imageFilter = null): string
    {
        if ($imageFilter !== null) {
            $channelRequestContext = $this->urlGenerator->getContext();
            $previousHost = $channelRequestContext->getHost();
            $channelHost = $channel->getHostname();
            Assert::string($channelHost);
            $channelRequestContext->setHost($channelHost);

            $imageUrl = $this->cacheManager->getBrowserPath(
                $imagePath,
                $imageFilter,
            );

            $channelRequestContext->setHost($previousHost);

            return $imageUrl;
        }
        // Fall back to the image path if no liip filter is provided, not recommended!

        $hostname = $channel->getHostname();
        Assert::notNull($hostname, 'The channel\'s hostname should not be null.');
        // TODO: is there any better way to handle this? Especially the media/image directory
        $urlPackage = new UrlPackage(
            $this->urlGenerator->getContext()->getScheme() . '://' . $hostname . (str_ends_with($hostname, '/') ? '' : '/') . 'media/image',
            new EmptyVersionStrategy(),
        );

        return $urlPackage->getUrl($imagePath);
    }
}
