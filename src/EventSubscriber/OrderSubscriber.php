<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\EventSubscriber;

use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Messenger\MessageBusInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Enqueuer\EcommerceOrderEnqueuerInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Message\EcommerceOrder\EcommerceOrderRemove;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface;

final class OrderSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private MessageBusInterface $messageBus,
        private EcommerceOrderEnqueuerInterface $ecommerceOrderEnqueuer
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'sylius.order.post_create' => ['enqueueOrder'],
            'sylius.order.post_update' => ['enqueueOrder'],
            'sylius.order.post_delete' => ['removeOrder'],
        ];
    }

    public function enqueueOrder(GenericEvent $event): void
    {
        $order = $event->getSubject();
        if (!$order instanceof OrderInterface || !$order instanceof ActiveCampaignAwareInterface) {
            return;
        }

        $this->ecommerceOrderEnqueuer->enqueue($order, false);
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
