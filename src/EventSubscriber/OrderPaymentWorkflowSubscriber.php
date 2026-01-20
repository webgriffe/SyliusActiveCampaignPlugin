<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\EventSubscriber;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderPaymentTransitions;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\CompletedEvent;
use Webgriffe\SyliusActiveCampaignPlugin\Enqueuer\RealTimeOrderEnqueuerInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface;

final class OrderPaymentWorkflowSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly RealTimeOrderEnqueuerInterface $realTimeOrderEnqueuer,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            sprintf(
                'workflow.%s.completed.%s',
                OrderPaymentTransitions::GRAPH,
                OrderPaymentTransitions::TRANSITION_PAY,
            ) => ['onOrderPaymentPaid', 400],
        ];
    }

    public function onOrderPaymentPaid(CompletedEvent $event): void
    {
        $order = $event->getSubject();

        if (!$order instanceof OrderInterface || !$order instanceof ActiveCampaignAwareInterface) {
            return;
        }

        $this->realTimeOrderEnqueuer->enqueue($order);
    }
}
