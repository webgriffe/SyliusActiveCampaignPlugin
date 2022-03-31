<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\EventSubscriber;

use Psr\Log\LoggerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Messenger\MessageBusInterface;
use Throwable;
use Webgriffe\SyliusActiveCampaignPlugin\Enqueuer\EcommerceOrderEnqueuerInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Message\EcommerceOrder\EcommerceOrderRemove;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface;

final class OrderSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private MessageBusInterface $messageBus,
        private EcommerceOrderEnqueuerInterface $ecommerceOrderEnqueuer,
        private LoggerInterface $logger
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'sylius.order.post_complete' => ['enqueueOrderInRealTime'],
            'sylius.order.post_create' => ['enqueueOrderNotInRealTime'],
            'sylius.order.post_update' => ['enqueueOrderNotInRealTime'],
            'sylius.order.post_delete' => ['removeOrder'],
        ];
    }

    public function enqueueOrderInRealTime(GenericEvent $event): void
    {
        $this->enqueueOrder($event, true);
    }

    public function enqueueOrderNotInRealTime(GenericEvent $event): void
    {
        $this->enqueueOrder($event, false);
    }

    private function enqueueOrder(GenericEvent $event, bool $isInRealTime): void
    {
        $order = $event->getSubject();
        if (!$order instanceof OrderInterface || !$order instanceof ActiveCampaignAwareInterface) {
            return;
        }

        try {
            $this->ecommerceOrderEnqueuer->enqueue($order, $isInRealTime);
        } catch (Throwable $throwable) {
            $this->logger->error($throwable->getMessage(), $throwable->getTrace());
        }
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
