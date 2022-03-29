<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Enqueuer;

use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Message\EcommerceOrder\EcommerceOrderCreate;
use Webgriffe\SyliusActiveCampaignPlugin\Message\EcommerceOrder\EcommerceOrderRemove;
use Webgriffe\SyliusActiveCampaignPlugin\Message\EcommerceOrder\EcommerceOrderUpdate;
use Webmozart\Assert\Assert;

final class EcommerceOrderEnqueuer implements EcommerceOrderEnqueuerInterface
{
    public function __construct(
        private MessageBusInterface $messageBus,
        private EntityManagerInterface $entityManager
    ) {
    }

    public function enqueue($order, bool $isInRealTime = true): void
    {
        /** @var string|int|null $orderId */
        $orderId = $order->getId();
        Assert::notNull($orderId, 'The order id should not be null');
        $activeCampaignEcommerceOrderId = $order->getActiveCampaignId();
        if ($activeCampaignEcommerceOrderId !== null) {
            if ($order->getState() === OrderInterface::STATE_CANCELLED) {
                $this->messageBus->dispatch(new EcommerceOrderRemove($activeCampaignEcommerceOrderId));
                $order->setActiveCampaignId(null);
                $this->entityManager->flush();

                return;
            }
            $this->messageBus->dispatch(new EcommerceOrderUpdate($orderId, $activeCampaignEcommerceOrderId, $isInRealTime));

            return;
        }
        if ($order->getState() === OrderInterface::STATE_CANCELLED) {
            return;
        }
        // TODO: Search for order number?

        $this->messageBus->dispatch(new EcommerceOrderCreate($orderId, $isInRealTime));
    }
}
