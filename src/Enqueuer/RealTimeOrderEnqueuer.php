<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Enqueuer;

use Psr\Log\LoggerInterface;
use Sylius\Component\Core\OrderPaymentStates;
use Sylius\Component\Order\Model\OrderInterface;

final class RealTimeOrderEnqueuer implements RealTimeOrderEnqueuerInterface
{
    public function __construct(
        private readonly EcommerceOrderEnqueuerInterface $ecommerceOrderEnqueuer,
        private readonly LoggerInterface $logger,
        private readonly bool $sendUnpaidOrders,
    ) {
    }

    public function enqueue($order): void
    {
        if ($order->getState() === OrderInterface::STATE_CART || $order->getState() === OrderInterface::STATE_CANCELLED) {
            return;
        }
        if ($this->sendUnpaidOrders) {
            return;
        }
        if ($order->getPaymentState() !== OrderPaymentStates::STATE_PAID) {
            return;
        }

        try {
            $this->ecommerceOrderEnqueuer->enqueue($order);
        } catch (\Throwable $throwable) {
            $this->logger->error($throwable->getMessage(), $throwable->getTrace());
        }
    }
}
