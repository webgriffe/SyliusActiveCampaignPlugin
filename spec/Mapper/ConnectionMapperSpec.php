<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusActiveCampaignPlugin\Mapper;

use Sylius\Component\Core\Model\ChannelInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Mapper\ConnectionMapper;
use PhpSpec\ObjectBehavior;
use Webgriffe\SyliusActiveCampaignPlugin\Mapper\ConnectionMapperInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\Connection;
use Webmozart\Assert\InvalidArgumentException;

class ConnectionMapperSpec extends ObjectBehavior
{
    public function let(
        ChannelInterface $channel
    ): void {
        $channel->getCode()->willReturn('ecommerce');
        $channel->getName()->willReturn('E-Commerce');

        $this->beConstructedWith();
    }

    public function it_is_initializable(): void
    {
        $this->shouldBeAnInstanceOf(ConnectionMapper::class);
    }

    public function it_implements_connection_mapper_interface(): void
    {
        $this->shouldImplement(ConnectionMapperInterface::class);
    }

    public function it_should_returns_an_active_campaign_connection_instance(ChannelInterface $channel): void
    {
        $this->mapFromChannel($channel)->shouldReturnAnInstanceOf(Connection::class);
    }

    public function it_throws_if_channel_has_no_code(ChannelInterface $channel): void
    {
        $channel->getCode()->willReturn(null);
        $this->shouldThrow(new InvalidArgumentException('The channel does not have a code.'))->during('mapFromChannel', [$channel]);
    }

    public function it_should_returns_an_active_connection_with_external_id_and_name(ChannelInterface $channel): void
    {
        $connection = $this->mapFromChannel($channel);
        $connection->getExternalId()->shouldReturn('ecommerce');
        $connection->getName()->shouldReturn('E-Commerce');
    }

    public function it_should_returns_an_active_connection_without_channel_name(ChannelInterface $channel): void
    {
        $channel->getName()->willReturn(null);
        $connection = $this->mapFromChannel($channel);
        $connection->getExternalId()->shouldReturn('ecommerce');
        $connection->getName()->shouldReturn('Sylius eCommerce');
    }
}
