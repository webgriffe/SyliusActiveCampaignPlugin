<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Mapper;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign\EcommerceOrderFactoryInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\EcommerceOrderInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface;
use Webmozart\Assert\Assert;

final class EcommerceOrderMapper implements EcommerceOrderMapperInterface
{
    public function __construct(
        private EcommerceOrderFactoryInterface $ecommerceOrderFactory
    ) {
    }

    public function mapFromOrder(OrderInterface $order): EcommerceOrderInterface
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

        return $ecommerceOrder;
    }
}
