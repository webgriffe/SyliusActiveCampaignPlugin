<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusActiveCampaignPlugin\Enqueuer;

use App\Entity\Channel\ChannelInterface;
use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignResourceClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Enqueuer\ConnectionEnqueuer;
use Webgriffe\SyliusActiveCampaignPlugin\Enqueuer\ConnectionEnqueuerInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Connection\ConnectionCreate;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Connection\ConnectionUpdate;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Connection\ConnectionResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ListResourcesResponseInterface;
use Webmozart\Assert\InvalidArgumentException;

class ConnectionEnqueuerSpec extends ObjectBehavior
{
    public function let(
        MessageBusInterface $messageBus,
        ActiveCampaignResourceClientInterface $activeCampaignConnectionClient,
        EntityManagerInterface $entityManager,
        ChannelInterface $channel,
        ListResourcesResponseInterface $listResourcesResponse
    ): void {
        $channel->getId()->willReturn(1);
        $channel->getActiveCampaignId()->willReturn(null);
        $channel->getCode()->willReturn('ecommerce');

        $activeCampaignConnectionClient->list([
            'filters[service]' => 'sylius',
            'filters[externalid]' => 'ecommerce',
        ])->willReturn($listResourcesResponse);

        $listResourcesResponse->getResourceResponseLists()->willReturn([]);

        $this->beConstructedWith($messageBus, $activeCampaignConnectionClient, $entityManager);
    }

    public function it_is_initializable(): void
    {
        $this->shouldBeAnInstanceOf(ConnectionEnqueuer::class);
    }

    public function it_implements_connection_enqueuer_interface(): void
    {
        $this->shouldImplement(ConnectionEnqueuerInterface::class);
    }

    public function it_throws_if_channel_id_is_null(ChannelInterface $channel): void
    {
        $channel->getId()->willReturn(null);
        $this->shouldThrow(new InvalidArgumentException('The channel id should not be null'))
            ->during('enqueue', [$channel]);
    }

    public function it_enqueues_a_connection_update_if_channel_connection_active_campaign_id_is_not_null(
        ChannelInterface $channel,
        MessageBusInterface $messageBus
    ): void {
        $channel->getActiveCampaignId()->willReturn(10);
        $messageBus->dispatch(Argument::type(ConnectionUpdate::class))->shouldBeCalledOnce()->willReturn(new Envelope(new ConnectionUpdate(1, 10)));

        $this->enqueue($channel);
    }

    public function it_throws_if_channel_code_is_null(ChannelInterface $channel): void
    {
        $channel->getCode()->willReturn(null);
        $this->shouldThrow(new InvalidArgumentException('The channel code should not be null'))
            ->during('enqueue', [$channel]);
    }

    public function it_enqueues_a_connection_update_if_channel_connection_active_campaign_id_is_null_and_a_connection_with_the_same_code_already_exists(
        ChannelInterface $channel,
        MessageBusInterface $messageBus,
        ListResourcesResponseInterface $listResourcesResponse,
        EntityManagerInterface $entityManager
    ): void {
        $listResourcesResponse->getResourceResponseLists()->willReturn([
            new ConnectionResponse(14),
        ]);
        $channel->setActiveCampaignId(14)->shouldBeCalledOnce();
        $entityManager->flush()->shouldBeCalledOnce();
        $messageBus->dispatch(Argument::type(ConnectionUpdate::class))->shouldBeCalledOnce()->willReturn(new Envelope(new ConnectionUpdate(1, 14)));

        $this->enqueue($channel);
    }

    public function it_enqueues_a_connection_update_using_the_first_match_if_channel_connection_active_campaign_id_is_null_and_a_connection_with_the_same_code_already_exists(
        ChannelInterface $channel,
        MessageBusInterface $messageBus,
        ListResourcesResponseInterface $listResourcesResponse,
        EntityManagerInterface $entityManager
    ): void {
        $listResourcesResponse->getResourceResponseLists()->willReturn([
            new ConnectionResponse(18),
            new ConnectionResponse(14),
        ]);
        $channel->setActiveCampaignId(18)->shouldBeCalledOnce();
        $entityManager->flush()->shouldBeCalledOnce();
        $messageBus->dispatch(Argument::type(ConnectionUpdate::class))->shouldBeCalledOnce()->willReturn(new Envelope(new ConnectionUpdate(1, 18)));

        $this->enqueue($channel);
    }

    public function it_enqueues_a_connection_create_if_both_channel_connection_active_campaign_id_is_null_and_a_connection_with_the_same_code_does_not_exist(
        ChannelInterface $channel,
        MessageBusInterface $messageBus,
        EntityManagerInterface $entityManager
    ): void {
        $channel->setActiveCampaignId(Argument::any())->shouldNotBeCalled();
        $entityManager->flush()->shouldNotBeCalled();
        $messageBus->dispatch(Argument::type(ConnectionCreate::class))->shouldBeCalledOnce()->willReturn(new Envelope(new ConnectionCreate(1)));

        $this->enqueue($channel);
    }
}
