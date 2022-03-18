<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Mapper;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Symfony\Component\Routing\RouterInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign\EcommerceOrderFactoryInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\EcommerceOrderDiscountInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\EcommerceOrderInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\EcommerceOrderProductInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface;
use Webmozart\Assert\Assert;

final class EcommerceOrderMapper implements EcommerceOrderMapperInterface
{
    public function __construct(
        private EcommerceOrderFactoryInterface $ecommerceOrderFactory,
        private RouterInterface $router,
        private EcommerceOrderProductMapperInterface $ecommerceOrderProductMapper,
        private EcommerceOrderDiscountMapperInterface $ecommerceOrderDiscountMapper
    ) {
    }

    public function mapFromOrder(OrderInterface $order, bool $isInRealTime): EcommerceOrderInterface
    {
        /** @var CustomerInterface|(CustomerInterface&ActiveCampaignAwareInterface)|null $customer */
        $customer = $order->getCustomer();
        Assert::isInstanceOf($customer, CustomerInterface::class, sprintf('Order customer should implement "%s".', CustomerInterface::class));
        Assert::isInstanceOf($customer, ActiveCampaignAwareInterface::class, sprintf('Order customer should implement "%s".', ActiveCampaignAwareInterface::class));
        $customerEmail = $customer->getEmail();
        Assert::notNull($customerEmail, 'The customer\'s email should not be null.');
        $ecommerceCustomerId = $customer->getActiveCampaignId();
        Assert::notNull($ecommerceCustomerId, 'The customer\'s ActiveCampaign customer id should not be null.');

        /** @var ChannelInterface|(ChannelInterface&ActiveCampaignAwareInterface)|null $channel */
        $channel = $order->getChannel();
        Assert::notNull($channel, 'Order does not have a channel.');
        Assert::isInstanceOf($channel, ActiveCampaignAwareInterface::class, sprintf('Order channel should implement "%s".', ActiveCampaignAwareInterface::class));
        $connectionId = $channel->getActiveCampaignId();
        Assert::notNull($connectionId, 'The channel\'s ActiveCampaign connection id should not be null.');

        $currencyCode = $order->getCurrencyCode();
        Assert::notNull($currencyCode, 'The order currency code should not be null.');

        $createdAt = $order->getCreatedAt();
        Assert::notNull($createdAt, 'The order creation date should not be null.');

        /** @var string|int|null $orderId */
        $orderId = $order->getId();
        Assert::notNull($orderId, 'The order id should not be null.');

        $ecommerceOrder = $this->ecommerceOrderFactory->createNew(
            $customerEmail,
            $connectionId,
            $ecommerceCustomerId,
            $currencyCode,
            $order->getTotal(),
            $createdAt,
            (string) $orderId,
            null,
            null
        );
        if (!$isInRealTime) {
            $ecommerceOrder->setSource(EcommerceOrderInterface::HISTORICAL_SOURCE_CODE);
        }
        $ecommerceOrder->setShippingAmount($order->getShippingTotal());
        $ecommerceOrder->setTaxAmount($order->getTaxTotal());
        $ecommerceOrder->setDiscountAmount($order->getOrderPromotionTotal());
        $ecommerceOrder->setOrderUrl($this->router->generate('sylius_shop_order_show', [
            'tokenValue' => $order->getTokenValue(),
            '_locale' => $order->getLocaleCode(),
        ]));
        $ecommerceOrder->setExternalUpdatedDate($order->getUpdatedAt());
        $firstShipment = $order->getShipments()->first();
        if ($firstShipment instanceof ShipmentInterface && (null !== $shippingMethod = $firstShipment->getMethod())) {
            $ecommerceOrder->setShippingMethod($shippingMethod->getName());
        }
        $ecommerceOrder->setOrderNumber($order->getNumber());

        /** @var EcommerceOrderProductInterface[] $orderProducts */
        $orderProducts = [];
        foreach ($order->getItems() as $orderItem) {
            $orderProducts[] = $this->ecommerceOrderProductMapper->mapFromOrderItem($orderItem);
        }
        $ecommerceOrder->setOrderProducts($orderProducts);

        /** @var EcommerceOrderDiscountInterface[] $orderDiscounts */
        $orderDiscounts = [];
        foreach ($order->getPromotions() as $promotion) {
            $orderDiscounts[] = $this->ecommerceOrderDiscountMapper->mapFromPromotion($order, $promotion);
        }
        $ecommerceOrder->setOrderDiscounts($orderDiscounts);

        return $ecommerceOrder;
    }
}
