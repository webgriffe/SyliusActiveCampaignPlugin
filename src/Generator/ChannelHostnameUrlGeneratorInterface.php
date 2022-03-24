<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Generator;

use Sylius\Component\Core\Model\ChannelInterface;

interface ChannelHostnameUrlGeneratorInterface
{
    public function generate(ChannelInterface $channel, string $routeName, array $parameters = []): string;
}
