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
use Webgriffe\SyliusActiveCampaignPlugin\Message\Connection\ConnectionCreate;
use Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\Connection\ConnectionCreateHandler;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ConnectionInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\CreateResourceResponseInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ResourceResponseInterface;

class ConnectionCreateHandlerSpec extends ObjectBehavior
{
    public function let(
        ConnectionMapperInterface $connectionMapper,
        ActiveCampaignResourceClientInterface $activeCampaignConnectionClient,
        ChannelRepositoryInterface $channelRepository,
        ChannelInterface $channel,
        ConnectionInterface $connection
    ): void {
        $connectionMapper->mapFromChannel($channel)->willReturn($connection);

        $channel->getActiveCampaignId()->willReturn(null);

        $channelRepository->find(1)->willReturn($channel);

        $this->beConstructedWith($connectionMapper, $activeCampaignConnectionClient, $channelRepository);
    }

    public function it_is_initializable(): void
    {
        $this->shouldBeAnInstanceOf(ConnectionCreateHandler::class);
    }

    public function it_throws_if_channel_is_not_found(
        ChannelRepositoryInterface $channelRepository
    ): void {
        $channelRepository->find(1)->shouldBeCalledOnce()->willReturn(null);

        $this->shouldThrow(new InvalidArgumentException('Channel with id "1" does not exists'))->during(
            '__invoke',
            [new ConnectionCreate(1)]
        );
    }

    public function it_throws_if_channel_is_not_an_implementation_of_active_campaign_aware_interface(
        ChannelRepositoryInterface $channelRepository,
        SyliusChannelInterface $syliusChannel
    ): void {
        $channelRepository->find(1)->shouldBeCalledOnce()->willReturn($syliusChannel);

        $this->shouldThrow(new InvalidArgumentException('The Channel entity should implement the "Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface" class'))->during(
            '__invoke',
            [new ConnectionCreate(1)]
        );
    }

    public function it_throws_if_channel_has_been_already_exported_to_active_campaign(
        ActiveCampaignAwareInterface $channel
    ): void {
        $channel->getActiveCampaignId()->willReturn(12);

        $this->shouldThrow(new InvalidArgumentException('The Channel with id "1" has been already created on ActiveCampaign on the connection with id "12"'))->during(
            '__invoke',
            [new ConnectionCreate(1)]
        );
    }

    public function it_creates_connection_on_active_campaign(
        ConnectionInterface $connection,
        ActiveCampaignResourceClientInterface $activeCampaignConnectionClient,
        ChannelInterface $channel,
        ChannelRepositoryInterface $channelRepository,
        CreateResourceResponseInterface $createConnectionResponse,
        ResourceResponseInterface $connectionResponse
    ): void {
        $connectionResponse->getId()->willReturn(12);
        $createConnectionResponse->getResourceResponse()->willReturn($connectionResponse);
        $activeCampaignConnectionClient->create($connection)->shouldBeCalledOnce()->willReturn($createConnectionResponse);
        $channel->setActiveCampaignId(12)->shouldBeCalledOnce();
        $channelRepository->add($channel)->shouldBeCalledOnce();

        $this->__invoke(new ConnectionCreate(1));
    }
}
