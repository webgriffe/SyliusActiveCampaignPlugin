<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\EventSubscriber;

use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Messenger\MessageBusInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Message\EcommerceOrder\EcommerceOrderCreate;
use Webgriffe\SyliusActiveCampaignPlugin\Message\EcommerceOrder\EcommerceOrderRemove;
use Webgriffe\SyliusActiveCampaignPlugin\Message\EcommerceOrder\EcommerceOrderUpdate;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface;

final class OrderSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private MessageBusInterface $messageBus
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'sylius.order.post_create' => ['createOrder'],
            'sylius.order.post_update' => ['updateOrder'],
            'sylius.order.post_delete' => ['removeOrder'],
        ];
    }

    public function createOrder(GenericEvent $event): void
    {
        $order = $event->getSubject();
        if (!$order instanceof OrderInterface) {
            return;
        }
        /** @var mixed $orderId */
        $orderId = $order->getId();
        if ($orderId === null) {
            return;
        }
        if (!is_int($orderId)) {
            $orderId = (string) $orderId;
        }

        $this->messageBus->dispatch(new EcommerceOrderCreate($orderId, true));
    }

    public function updateOrder(GenericEvent $event): void
    {
        $order = $event->getSubject();
        if (!$order instanceof OrderInterface || !$order instanceof ActiveCampaignAwareInterface) {
            return;
        }
        /** @var mixed $orderId */
        $orderId = $order->getId();
        if ($orderId === null) {
            return;
        }
        if (!is_int($orderId)) {
            $orderId = (string) $orderId;
        }
        $activeCampaignId = $order->getActiveCampaignId();
        if ($activeCampaignId === null) {
            return;
        }

        $this->messageBus->dispatch(new EcommerceOrderUpdate($orderId, $activeCampaignId, true));
    }

    public function removeOrder(GenericEvent $event): void
    {
        $customer = $event->getSubject();
        if (!$customer instanceof ActiveCampaignAwareInterface) {
            return;
        }
        $activeCampaignId = $customer->getActiveCampaignId();
        if ($activeCampaignId === null) {
            return;
        }

        $this->messageBus->dispatch(new EcommerceOrderRemove($activeCampaignId));
    }
}
