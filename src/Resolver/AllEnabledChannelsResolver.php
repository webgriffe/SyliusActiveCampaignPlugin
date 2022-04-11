<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Resolver;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Repository\ActiveCampaignResourceRepositoryInterface;
use Webmozart\Assert\Assert;

final class AllEnabledChannelsResolver implements CustomerChannelsResolverInterface
{
    public function __construct(private ActiveCampaignResourceRepositoryInterface $channelRepository)
    {
    }

    public function resolve(CustomerInterface $customer): array
    {
        $channels = $this->channelRepository->findAllToEnqueue();
        Assert::allIsInstanceOf($channels, ChannelInterface::class);

        return $channels;
    }
}
