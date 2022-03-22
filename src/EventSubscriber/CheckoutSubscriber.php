<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\EventSubscriber;

use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Messenger\MessageBusInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Message\EcommerceOrder\EcommerceOrderCreate;
use Webgriffe\SyliusActiveCampaignPlugin\Message\EcommerceOrder\EcommerceOrderUpdate;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface;

final class CheckoutSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private MessageBusInterface $messageBus
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'sylius.order.post_complete' => ['createOrUpdateOrder'],
        ];
    }

    public function createOrUpdateOrder(GenericEvent $event): void
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
        if ($order instanceof ActiveCampaignAwareInterface && ($activeCampaignId = $order->getActiveCampaignId()) !== null) {
            $this->messageBus->dispatch(new EcommerceOrderUpdate($orderId, $activeCampaignId, true));

            return;
        }

        $this->messageBus->dispatch(new EcommerceOrderCreate($orderId, true));
    }
}
