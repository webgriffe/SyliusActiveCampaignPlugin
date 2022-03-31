<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Resolver;

use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Webmozart\Assert\Assert;

final class AllEnabledChannelsResolver implements CustomerChannelsResolverInterface
{
    private ChannelRepositoryInterface $channelRepository;

    public function __construct(ChannelRepositoryInterface $channelRepository)
    {
        $this->channelRepository = $channelRepository;
    }

    /**
     * @inheritDoc
     */
    public function resolve(CustomerInterface $customer): array
    {
        $channels = $this->channelRepository->findBy(['enabled' => true]);
        Assert::allIsInstanceOf($channels, ChannelInterface::class);

        return $channels;
    }
}
