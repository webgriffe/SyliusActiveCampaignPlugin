<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusActiveCampaignPlugin\Enqueuer;

use App\Entity\Channel\ChannelInterface;
use App\Entity\Order\OrderInterface;
use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\ChannelInterface as SyliusChannelInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignResourceClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Enqueuer\EcommerceOrderEnqueuer;
use Webgriffe\SyliusActiveCampaignPlugin\Enqueuer\EcommerceOrderEnqueuerInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Message\EcommerceOrder\EcommerceOrderCreate;
use Webgriffe\SyliusActiveCampaignPlugin\Message\EcommerceOrder\EcommerceOrderRemove;
use Webgriffe\SyliusActiveCampaignPlugin\Message\EcommerceOrder\EcommerceOrderUpdate;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\EcommerceOrder\EcommerceOrderResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ListResourcesResponseInterface;
use Webmozart\Assert\InvalidArgumentException;

class EcommerceOrderEnqueuerSpec extends ObjectBehavior
{
    public function let(
        MessageBusInterface $messageBus,
        EntityManagerInterface $entityManager,
        ActiveCampaignResourceClientInterface $activeCampaignEcommerceOrderClient,
        OrderInterface $order,
        ChannelInterface $channel
    ): void {
        $order->getId()->willReturn(1);
        $order->getActiveCampaignId()->willReturn(null);
        $order->getState()->willReturn(OrderInterface::STATE_NEW);
        $order->getChannel()->willReturn($channel);

        $channel->getActiveCampaignId()->willReturn(153);

        $this->beConstructedWith($messageBus, $entityManager, $activeCampaignEcommerceOrderClient);
    }

    public function it_is_initializable(): void
    {
        $this->shouldBeAnInstanceOf(EcommerceOrderEnqueuer::class);
    }

    public function it_implements_ecommerce_order_enqueuer_interface(): void
    {
        $this->shouldImplement(EcommerceOrderEnqueuerInterface::class);
    }

    public function it_throws_if_order_id_is_null(OrderInterface $order): void
    {
        $order->getId()->willReturn(null);
        $this->shouldThrow(new InvalidArgumentException('The order id should not be null'))
            ->during('enqueue', [$order]);
    }

    public function it_enqueues_a_ecommerce_order_remove_if_order_ecommerce_order_active_campaign_id_is_not_null_and_state_is_canceled(
        OrderInterface $order,
        MessageBusInterface $messageBus,
        EntityManagerInterface $entityManager
    ): void {
        $order->getState()->willReturn(OrderInterface::STATE_CANCELLED);
        $order->getActiveCampaignId()->willReturn(10);
        $order->setActiveCampaignId(null)->shouldBeCalledOnce();
        $entityManager->flush()->shouldBeCalledOnce();
        $messageBus->dispatch(Argument::type(EcommerceOrderRemove::class))->shouldBeCalledOnce()->willReturn(new Envelope(new EcommerceOrderRemove(10)));

        $this->enqueue($order);
    }

    public function it_enqueues_a_ecommerce_order_update_if_order_ecommerce_order_active_campaign_id_is_not_null_and_state_is_not_canceled(
        OrderInterface $order,
        MessageBusInterface $messageBus
    ): void {
        $order->getActiveCampaignId()->willReturn(10);
        $messageBus->dispatch(Argument::type(EcommerceOrderUpdate::class))->shouldBeCalledOnce()->willReturn(new Envelope(new EcommerceOrderUpdate(1, 10)));

        $this->enqueue($order);
    }

    public function it_throws_if_order_channel_is_null(OrderInterface $order): void
    {
        $order->getChannel()->willReturn(null);
        $this->shouldThrow(new InvalidArgumentException('The order channel should implements "Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface"'))
            ->during('enqueue', [$order]);
    }

    public function it_throws_if_order_channel_is_not_an_instance_of_active_campaign_aware(OrderInterface $order, SyliusChannelInterface $syliusChannel): void
    {
        $order->getChannel()->willReturn($syliusChannel);
        $this->shouldThrow(new InvalidArgumentException('The order channel should implements "Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface"'))
            ->during('enqueue', [$order]);
    }

    public function it_throws_if_order_channel_active_campaign_id_is_null(OrderInterface $order, ActiveCampaignAwareInterface $channel): void
    {
        $channel->getActiveCampaignId()->willReturn(null);
        $this->shouldThrow(new InvalidArgumentException('The channel ActiveCampaign connection id should not be null'))
            ->during('enqueue', [$order]);
    }

    public function it_enqueues_a_ecommerce_order_remove_if_order_ecommerce_order_active_campaign_id_is_null_and_state_is_canceled_and_it_is_found_on_active_campaign(
        OrderInterface $order,
        MessageBusInterface $messageBus,
        EntityManagerInterface $entityManager,
        ActiveCampaignResourceClientInterface $activeCampaignEcommerceOrderClient,
        ListResourcesResponseInterface $listResourcesResponse
    ): void {
        $order->getState()->willReturn(OrderInterface::STATE_CANCELLED);
        $order->setActiveCampaignId(null)->shouldNotBeCalled();
        $entityManager->flush()->shouldNotBeCalled();

        $activeCampaignEcommerceOrderClient->list([
            'filters[connectionid]' => 153,
            'filters[externalid]' => 1,
        ])->shouldBeCalledOnce()->willReturn($listResourcesResponse);

        $listResourcesResponse->getResourceResponseLists()->shouldBeCalledOnce()->willReturn([
            new EcommerceOrderResponse(14)
        ]);

        $messageBus->dispatch(Argument::type(EcommerceOrderRemove::class))->shouldBeCalledOnce()->willReturn(new Envelope(new EcommerceOrderRemove(14)));

        $this->enqueue($order);
    }

    public function it_enqueues_a_ecommerce_order_update_if_order_ecommerce_order_active_campaign_id_is_null_and_state_is_not_canceled_and_it_is_found_on_active_campaign(
        OrderInterface $order,
        MessageBusInterface $messageBus,
        EntityManagerInterface $entityManager,
        ActiveCampaignResourceClientInterface $activeCampaignEcommerceOrderClient,
        ListResourcesResponseInterface $listResourcesResponse
    ): void {
        $order->getState()->willReturn(OrderInterface::STATE_NEW);
        $order->setActiveCampaignId(14)->shouldBeCalledOnce();
        $entityManager->flush()->shouldBeCalledOnce();

        $activeCampaignEcommerceOrderClient->list([
            'filters[connectionid]' => 153,
            'filters[externalid]' => 1,
        ])->shouldBeCalledOnce()->willReturn($listResourcesResponse);

        $listResourcesResponse->getResourceResponseLists()->shouldBeCalledOnce()->willReturn([
            new EcommerceOrderResponse(14),
            new EcommerceOrderResponse(18),
        ]);

        $messageBus->dispatch(Argument::type(EcommerceOrderUpdate::class))->shouldBeCalledOnce()->willReturn(new Envelope(new EcommerceOrderUpdate(1,14, false)));

        $this->enqueue($order, false);
    }

    public function it_search_for_external_checkout_id_if_order_state_is_cart(
        OrderInterface $order,
        MessageBusInterface $messageBus,
        ActiveCampaignResourceClientInterface $activeCampaignEcommerceOrderClient,
        ListResourcesResponseInterface $listResourcesResponse
    ): void {
        $order->getState()->willReturn(OrderInterface::STATE_CART);

        $activeCampaignEcommerceOrderClient->list([
            'filters[connectionid]' => 153,
            'filters[externalcheckoutid]' => 1,
        ])->shouldBeCalledOnce()->willReturn($listResourcesResponse);

        $listResourcesResponse->getResourceResponseLists()->shouldBeCalledOnce()->willReturn([]);

        $messageBus->dispatch(Argument::type(EcommerceOrderCreate::class))->shouldBeCalledOnce()->willReturn(new Envelope(new EcommerceOrderCreate(1)));

        $this->enqueue($order);
    }

    public function it_enqueues_a_ecommerce_order_create_if_order_ecommerce_order_active_campaign_id_is_null_and_state_is_not_canceled(
        OrderInterface $order,
        MessageBusInterface $messageBus,
        EntityManagerInterface $entityManager,
        ActiveCampaignResourceClientInterface $activeCampaignEcommerceOrderClient,
        ListResourcesResponseInterface $listResourcesResponse
    ): void {
        $order->setActiveCampaignId(null)->shouldNotBeCalled();
        $entityManager->flush()->shouldNotBeCalled();

        $activeCampaignEcommerceOrderClient->list([
            'filters[connectionid]' => 153,
            'filters[externalid]' => 1,
        ])->shouldBeCalledOnce()->willReturn($listResourcesResponse);

        $listResourcesResponse->getResourceResponseLists()->shouldBeCalledOnce()->willReturn([]);

        $messageBus->dispatch(Argument::type(EcommerceOrderCreate::class))->shouldBeCalledOnce()->willReturn(new Envelope(new EcommerceOrderCreate(1)));

        $this->enqueue($order);
    }
}
