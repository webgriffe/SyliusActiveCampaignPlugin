<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\Connection;

use App\Entity\Channel\ChannelInterface;
use InvalidArgumentException;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface as SyliusChannelInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignResourceClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Mapper\ConnectionMapperInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Connection\ConnectionUpdate;
use Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\Connection\ConnectionUpdateHandler;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ConnectionInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\UpdateResourceResponseInterface;

class ConnectionUpdateHandlerSpec extends ObjectBehavior
{
    public function let(
        ConnectionMapperInterface $connectionMapper,
        ConnectionInterface $connection,
        ChannelInterface $channel,
        ActiveCampaignResourceClientInterface $activeCampaignConnectionClient,
        ChannelRepositoryInterface $channelRepository
    ): void {
        $connectionMapper->mapFromChannel($channel)->willReturn($connection);

        $channel->getActiveCampaignId()->willReturn(1);

        $channelRepository->find(1)->willReturn($channel);

        $this->beConstructedWith($connectionMapper, $activeCampaignConnectionClient, $channelRepository);
    }

    public function it_is_initializable(): void
    {
        $this->shouldBeAnInstanceOf(ConnectionUpdateHandler::class);
    }

    public function it_throws_if_channel_is_not_found(
        ChannelRepositoryInterface $channelRepository
    ): void {
        $channelRepository->find(1)->shouldBeCalledOnce()->willReturn(null);

        $this->shouldThrow(new InvalidArgumentException('Channel with id "1" does not exists.'))->during(
            '__invoke', [new ConnectionUpdate(1, 1)]
        );
    }

    public function it_throws_if_channel_is_not_an_implementation_of_active_campaign_aware_interface(
        ChannelRepositoryInterface $channelRepository,
        SyliusChannelInterface $syliusChannel
    ): void {
        $channelRepository->find(1)->shouldBeCalledOnce()->willReturn($syliusChannel);

        $this->shouldThrow(new InvalidArgumentException('The Channel entity should implement the "Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface" class.'))->during(
            '__invoke', [new ConnectionUpdate(1, 1)]
        );
    }

    public function it_throws_if_channel_has_has_a_different_id_on_active_campaign_than_the_message_provided(
        ActiveCampaignAwareInterface $channel
    ): void {
        $channel->getActiveCampaignId()->willReturn(312);

        $this->shouldThrow(new InvalidArgumentException('The Channel with id "1" has an ActiveCampaign id that does not match. Expected "1", given "312".'))->during(
            '__invoke', [new ConnectionUpdate(1, 1)]
        );
    }

    public function it_updates_connection_on_active_campaign(
        ConnectionInterface $connection,
        ActiveCampaignResourceClientInterface $activeCampaignConnectionClient,
        UpdateResourceResponseInterface $updateConnectionResponse
    ): void {
        $activeCampaignConnectionClient->update(1, $connection)->shouldBeCalledOnce()->willReturn($updateConnectionResponse);

        $this->__invoke(new ConnectionUpdate(1, 1));
    }
}
