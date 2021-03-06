<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusActiveCampaignPlugin\Resolver;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Repository\ActiveCampaignResourceRepositoryInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Resolver\EnqueuableChannelsResolver;
use Webgriffe\SyliusActiveCampaignPlugin\Resolver\CustomerChannelsResolverInterface;

final class EnqueuableChannelsResolverSpec extends ObjectBehavior
{
    public function let(
        ActiveCampaignResourceRepositoryInterface $channelRepository,
    ): void {
        $this->beConstructedWith($channelRepository);
    }

    public function it_is_initializable(): void
    {
        $this->shouldBeAnInstanceOf(EnqueuableChannelsResolver::class);
    }

    public function it_implements_customer_channels_resolver_interface(): void
    {
        $this->shouldImplement(CustomerChannelsResolverInterface::class);
    }

    public function it_resolves_enqueuable_channels(
        ActiveCampaignResourceRepositoryInterface $channelRepository,
        CustomerInterface $customer,
        ChannelInterface $channel1,
        ChannelInterface $channel2
    ): void {
        $channelRepository->findAllToEnqueue()->shouldBeCalledOnce()->willReturn([$channel1, $channel2]);

        $this->resolve($customer)->shouldReturn([$channel1, $channel2]);
    }
}
