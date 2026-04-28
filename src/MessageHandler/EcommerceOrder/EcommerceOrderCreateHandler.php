<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\EcommerceOrder;

use GuzzleHttp\Exception\GuzzleException;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderPaymentStates;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Messenger\MessageBusInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignResourceClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Mapper\EcommerceOrderMapperInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Message\EcommerceOrder\EcommerceOrderCreate;
use Webgriffe\SyliusActiveCampaignPlugin\Message\EcommerceOrder\EcommerceOrderUpdate;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\EcommerceOrder\EcommerceOrderResponse;
use Webmozart\Assert\Assert;

final class EcommerceOrderCreateHandler
{
    public function __construct(
        private EcommerceOrderMapperInterface $ecommerceOrderMapper,
        private ActiveCampaignResourceClientInterface $activeCampaignEcommerceOrderClient,
        private OrderRepositoryInterface $orderRepository,
        private ?LoggerInterface $logger = null,
        private ?MessageBusInterface $messageBus = null,
    ) {
        if ($this->logger === null) {
            trigger_deprecation(
                'webgriffe/sylius-active-campaign-plugin',
                'v0.12.2',
                'The logger argument is mandatory.',
            );
        }
    }

    /**
     * @throws GuzzleException
     * @throws \Throwable
     * @throws \JsonException
     */
    public function __invoke(EcommerceOrderCreate $message): void
    {
        $orderId = $message->getOrderId();
        /** @var OrderInterface|null $order */
        $order = $this->orderRepository->find($orderId);
        if ($order === null) {
            throw new InvalidArgumentException(sprintf('Order with id "%s" does not exists', $orderId));
        }
        if (!$order instanceof ActiveCampaignAwareInterface) {
            throw new InvalidArgumentException(sprintf('The Order entity should implement the "%s" class', ActiveCampaignAwareInterface::class));
        }

        $activeCampaignId = $order->getActiveCampaignId();
        if ($activeCampaignId !== null) {
            $this->logger?->warning(sprintf(
                'The Order with id "%s" has been already created on ActiveCampaign on the ecommerce order with id "%s". Skipping creation.',
                $orderId,
                $activeCampaignId,
            ));

            return;
        }

        try {
            $activeCampaignOrderId = $this->activeCampaignEcommerceOrderClient->create($this->ecommerceOrderMapper->mapFromOrder($order, $message->isInRealTime()))->getResourceResponse()->getId();
            $linkedExistingOrder = false;
        } catch (UnprocessableEntityHttpException $e) {
            $channel = $order->getChannel();
            Assert::isInstanceOf($channel, ActiveCampaignAwareInterface::class);
            $searchOrders = $this->activeCampaignEcommerceOrderClient->list([
                'filters[connectionid]' => (string) $channel->getActiveCampaignId(),
                'filters[' . ($this->isOrderStillACart($order) ? 'externalcheckoutid' : 'externalid') . ']' => (string) $orderId,
            ])->getResourceResponseLists();
            if (count($searchOrders) < 1) {
                throw $e;
            }
            /** @var EcommerceOrderResponse $existingOrder */
            $existingOrder = reset($searchOrders);
            $activeCampaignOrderId = $existingOrder->getId();
            $linkedExistingOrder = true;
            $this->logger?->warning(sprintf(
                'EcommerceOrder with token "%s" already exists on ActiveCampaign with id "%s". Why it has not been found before?',
                (string) $order->getTokenValue(),
                $activeCampaignOrderId,
            ));
        } catch (\Throwable $e) {
            $this->logger?->error($e->getMessage(), $e->getTrace());

            throw $e;
        }
        $order->setActiveCampaignId($activeCampaignOrderId);
        $this->orderRepository->add($order);
        if ($linkedExistingOrder) {
            $this->messageBus?->dispatch(new EcommerceOrderUpdate($message->getOrderId(), $activeCampaignOrderId, $message->isInRealTime()));
        }
    }

    private function isOrderStillACart(OrderInterface $order): bool
    {
        if ($order->getState() === OrderInterface::STATE_CART) {
            return true;
        }

        return $order->getPaymentState() !== OrderPaymentStates::STATE_PAID;
    }
}
