<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusActiveCampaignPlugin\Enqueuer;

use App\Entity\Order\OrderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Prophecy\Argument;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Enqueuer\EcommerceOrderEnqueuer;
use PhpSpec\ObjectBehavior;
use Webgriffe\SyliusActiveCampaignPlugin\Enqueuer\EcommerceOrderEnqueuerInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Message\EcommerceOrder\EcommerceOrderCreate;
use Webgriffe\SyliusActiveCampaignPlugin\Message\EcommerceOrder\EcommerceOrderRemove;
use Webgriffe\SyliusActiveCampaignPlugin\Message\EcommerceOrder\EcommerceOrderUpdate;
use Webmozart\Assert\InvalidArgumentException;

class EcommerceOrderEnqueuerSpec extends ObjectBehavior
{
    public function let(
        MessageBusInterface $messageBus,
        EntityManagerInterface $entityManager,
        OrderInterface $order
    ): void {
        $order->getId()->willReturn(1);
        $order->getActiveCampaignId()->willReturn(null);
        $order->getState()->willReturn(OrderInterface::STATE_NEW);

        $this->beConstructedWith($messageBus, $entityManager);
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

    public function it_enqueues_a_ecommerce_order_create_if_order_ecommerce_order_active_campaign_id_is_null_and_state_is_not_canceled(
        OrderInterface $order,
        MessageBusInterface $messageBus,
        EntityManagerInterface $entityManager
    ): void {
        $order->setActiveCampaignId(null)->shouldNotBeCalled();
        $entityManager->flush()->shouldNotBeCalled();
        $messageBus->dispatch(Argument::type(EcommerceOrderCreate::class))->shouldBeCalledOnce()->willReturn(new Envelope(new EcommerceOrderCreate(1)));

        $this->enqueue($order);
    }
}
