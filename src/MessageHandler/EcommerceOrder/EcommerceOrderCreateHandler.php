<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\EcommerceOrder;

use InvalidArgumentException;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignResourceClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Mapper\EcommerceOrderMapperInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Message\EcommerceOrder\EcommerceOrderCreate;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\CreateResourceResponseInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\EcommerceOrder\CreateEcommerceOrderResponse;
use Webmozart\Assert\Assert;

final class EcommerceOrderCreateHandler
{
    public function __construct(
        private EcommerceOrderMapperInterface $ecommerceOrderMapper,
        private ActiveCampaignResourceClientInterface $activeCampaignEcommerceOrderClient,
        private OrderRepositoryInterface $orderRepository
    ) {
    }

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
            throw new InvalidArgumentException(sprintf('The Order with id "%s" has been already created on ActiveCampaign on the ecommerce order with id "%s"', $orderId, $activeCampaignId));
        }
        /** @var CreateResourceResponseInterface|CreateEcommerceOrderResponse $response */
        $response = $this->activeCampaignEcommerceOrderClient->create($this->ecommerceOrderMapper->mapFromOrder($order, $message->isInRealTime()));
        Assert::isInstanceOf($response, CreateEcommerceOrderResponse::class);
        $order->setActiveCampaignId($response->getOrder()->getId());
        $this->orderRepository->add($order);
    }
}
