<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Generator;

use Sylius\Component\Core\Model\ChannelInterface;

interface ChannelHostnameUrlGeneratorInterface
{
    public function generateForRoute(ChannelInterface $channel, string $routeName, array $parameters = []): string;

    public function generateForImage(ChannelInterface $channel, string $imagePath, ?string $imageFilter = null): string;
}
