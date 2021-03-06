<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Resolver;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Repository\ActiveCampaignResourceRepositoryInterface;

final class EnqueuableChannelsResolver implements CustomerChannelsResolverInterface
{
    /** @param ActiveCampaignResourceRepositoryInterface<ChannelInterface> $channelRepository */
    public function __construct(private ActiveCampaignResourceRepositoryInterface $channelRepository)
    {
    }

    public function resolve(CustomerInterface $customer): array
    {
        return $this->channelRepository->findAllToEnqueue();
    }
}
