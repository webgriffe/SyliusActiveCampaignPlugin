<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Enqueuer;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignResourceClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Message\EcommerceOrder\EcommerceOrderCreate;
use Webgriffe\SyliusActiveCampaignPlugin\Message\EcommerceOrder\EcommerceOrderRemove;
use Webgriffe\SyliusActiveCampaignPlugin\Message\EcommerceOrder\EcommerceOrderUpdate;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\EcommerceOrder\EcommerceOrderResponse;
use Webmozart\Assert\Assert;

final class EcommerceOrderEnqueuer implements EcommerceOrderEnqueuerInterface
{
    public function __construct(
        private MessageBusInterface $messageBus,
        private EntityManagerInterface $entityManager,
        private ActiveCampaignResourceClientInterface $activeCampaignEcommerceOrderClient,
        private ?LoggerInterface $logger = null,
    ) {
        if ($this->logger === null) {
            trigger_deprecation(
                'webgriffe/sylius-active-campaign-plugin',
                'v0.12.2',
                'The logger argument is mandatory.',
            );
        }
    }

    public function enqueue($order, bool $isInRealTime = true): void
    {
        /** @var string|int|null $orderId */
        $orderId = $order->getId();
        Assert::notNull($orderId, 'The order id should not be null');
        $this->logger?->debug(sprintf(
            'Starting enqueuing ecommerce order for order "%s".',
            $orderId,
        ));
        $activeCampaignEcommerceOrderId = $order->getActiveCampaignId();
        if ($activeCampaignEcommerceOrderId !== null) {
            if ($order->getState() === OrderInterface::STATE_CANCELLED) {
                $this->logger?->debug(sprintf(
                    'Order "%s" has been cancelled, so we have to remove the ecommerce order "%s".',
                    $orderId,
                    $activeCampaignEcommerceOrderId,
                ));
                $this->messageBus->dispatch(new EcommerceOrderRemove($activeCampaignEcommerceOrderId));
                $order->setActiveCampaignId(null);
                $this->entityManager->flush();

                return;
            }
            $this->logger?->debug(sprintf(
                'Order "%s" has an already valued ActiveCampaign id "%s", so we have to update the ecommerce order.',
                $orderId,
                $activeCampaignEcommerceOrderId,
            ));
            $this->messageBus->dispatch(new EcommerceOrderUpdate($orderId, $activeCampaignEcommerceOrderId, $isInRealTime));

            return;
        }
        /** @var ActiveCampaignAwareInterface|null $channel */
        $channel = $order->getChannel();
        Assert::isInstanceOf($channel, ActiveCampaignAwareInterface::class, sprintf('The order channel should implements "%s"', ActiveCampaignAwareInterface::class));
        $channelActiveCampaignId = $channel->getActiveCampaignId();
        Assert::notNull($channelActiveCampaignId, 'The channel ActiveCampaign connection id should not be null');
        $ecommerceOrdersWithSameConnectionAndId = $this->activeCampaignEcommerceOrderClient->list([
            'filters[connectionid]' => (string) $channelActiveCampaignId,
            'filters[' . ($order->getState() === OrderInterface::STATE_CART ? 'externalcheckoutid' : 'externalid') . ']' => (string) $orderId,
        ])->getResourceResponseLists();
        if (count($ecommerceOrdersWithSameConnectionAndId) > 0) {
            /** @var EcommerceOrderResponse $ecommerceOrder */
            $ecommerceOrder = reset($ecommerceOrdersWithSameConnectionAndId);
            $activeCampaignEcommerceOrderId = $ecommerceOrder->getId();

            if ($order->getState() === OrderInterface::STATE_CANCELLED) {
                $this->logger?->debug(sprintf(
                    'Found an ActiveCampaign ecommerce order with id "%s" for given canceled order "%s", we have to remove the ecommerce order.',
                    $activeCampaignEcommerceOrderId,
                    $orderId,
                ));
                $this->messageBus->dispatch(new EcommerceOrderRemove($activeCampaignEcommerceOrderId));

                return;
            }
            $this->logger?->debug(sprintf(
                'Found an ActiveCampaign ecommerce order with id "%s" for given order "%s", the id has been stored and we have to update the ecommerce order.',
                $activeCampaignEcommerceOrderId,
                $orderId,
            ));
            $order->setActiveCampaignId($activeCampaignEcommerceOrderId);
            $this->entityManager->flush();

            $this->messageBus->dispatch(new EcommerceOrderUpdate($orderId, $activeCampaignEcommerceOrderId, $isInRealTime));

            return;
        }
        if ($order->getState() === OrderInterface::STATE_CANCELLED) {
            $this->logger?->debug(sprintf(
                'No ecommerce order found for given canceled order "%s", we can avoid creating the ecommerce order.',
                $orderId,
            ));

            return;
        }
        $this->logger?->debug(sprintf(
            'No ecommerce order found for given order "%s", we have to create the ecommerce order.',
            $orderId,
        ));

        $this->messageBus->dispatch(new EcommerceOrderCreate($orderId, $isInRealTime));
    }
}
