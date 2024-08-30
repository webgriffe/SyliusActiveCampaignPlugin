<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\EcommerceOrder;

use GuzzleHttp\Exception\GuzzleException;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignResourceClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Mapper\EcommerceOrderMapperInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Message\EcommerceOrder\EcommerceOrderUpdate;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface;

final class EcommerceOrderUpdateHandler
{
    public function __construct(
        private EcommerceOrderMapperInterface $ecommerceOrderMapper,
        private ActiveCampaignResourceClientInterface $activeCampaignEcommerceOrderClient,
        private OrderRepositoryInterface $orderRepository,
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

    /**
     * @throws GuzzleException
     * @throws \Throwable
     * @throws \JsonException
     */
    public function __invoke(EcommerceOrderUpdate $message): void
    {
        $orderId = $message->getOrderId();
        /** @var OrderInterface|null $order */
        $order = $this->orderRepository->find($orderId);
        if ($order === null) {
            throw new InvalidArgumentException(sprintf('Order with id "%s" does not exists.', $orderId));
        }
        if (!$order instanceof ActiveCampaignAwareInterface) {
            throw new InvalidArgumentException(sprintf('The Order entity should implement the "%s" class.', ActiveCampaignAwareInterface::class));
        }

        $activeCampaignId = $order->getActiveCampaignId();
        if ($activeCampaignId !== $message->getActiveCampaignId()) {
            throw new InvalidArgumentException(sprintf('The Order with id "%s" has an ActiveCampaign id that does not match. Expected "%s", given "%s".', $orderId, $message->getActiveCampaignId(), (string) $activeCampaignId));
        }

        try {
            $this->activeCampaignEcommerceOrderClient->update($message->getActiveCampaignId(), $this->ecommerceOrderMapper->mapFromOrder($order, $message->isInRealTime()));
        } catch (\Throwable $e) {
            $this->logger?->error($e->getMessage(), $e->getTrace());

            throw $e;
        }
    }
}
