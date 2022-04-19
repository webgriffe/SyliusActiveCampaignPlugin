<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusActiveCampaignPlugin\Mapper;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ChannelInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign\ConnectionFactoryInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Mapper\ConnectionMapper;
use Webgriffe\SyliusActiveCampaignPlugin\Mapper\ConnectionMapperInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ConnectionInterface;
use Webmozart\Assert\InvalidArgumentException;

class ConnectionMapperSpec extends ObjectBehavior
{
    public function let(
        ChannelInterface $channel,
        ConnectionInterface $connection,
        ConnectionFactoryInterface $connectionFactory
    ): void {
        $channel->getCode()->willReturn('ecommerce');
        $channel->getName()->willReturn('E-Commerce');

        $connectionFactory->createNew('sylius', 'ecommerce', 'E-Commerce')->willReturn($connection);

        $this->beConstructedWith($connectionFactory);
    }

    public function it_is_initializable(): void
    {
        $this->shouldBeAnInstanceOf(ConnectionMapper::class);
    }

    public function it_implements_connection_mapper_interface(): void
    {
        $this->shouldImplement(ConnectionMapperInterface::class);
    }

    public function it_should_returns_an_active_campaign_connection_instance(ChannelInterface $channel, ConnectionInterface $connection): void
    {
        $this->mapFromChannel($channel)->shouldReturn($connection);
    }

    public function it_throws_if_channel_has_no_code(ChannelInterface $channel): void
    {
        $channel->getCode()->willReturn(null);
        $this->shouldThrow(new InvalidArgumentException('The channel does not have a code.'))->during('mapFromChannel', [$channel]);
    }

    public function it_should_returns_an_active_connection_without_channel_name(
        ChannelInterface $channel,
        ConnectionInterface $connection,
        ConnectionFactoryInterface $connectionFactory
    ): void {
        $channel->getName()->willReturn(null);
        $connectionFactory->createNew('sylius', 'ecommerce', 'Sylius eCommerce')->shouldBeCalledOnce()->willReturn($connection);
        $this->mapFromChannel($channel)->shouldReturn($connection);
    }
}
