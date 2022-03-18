<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Mapper;

use Sylius\Component\Core\Model\OrderItemInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign\EcommerceOrderProductFactoryInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\EcommerceOrderProductInterface;
use Webmozart\Assert\Assert;

final class EcommerceOrderProductMapper implements EcommerceOrderProductMapperInterface
{
    public function __construct(
        private EcommerceOrderProductFactoryInterface $ecommerceOrderProductFactory
    ) {
    }

    public function mapFromOrderItem(OrderItemInterface $orderItem): EcommerceOrderProductInterface
    {
        $productName = $orderItem->getProductName();
        Assert::notNull($productName, 'The order item\'s product name should not be null.');
        $product = $orderItem->getProduct();
        Assert::notNull($product, 'The order item\'s product should not be null.');
        /** @var string|int|null $productId */
        $productId = $product->getId();
        Assert::notNull($productId, 'The order item\'s product id should not be null.');
        $ecommerceOrderProduct = $this->ecommerceOrderProductFactory->createNew(
            $productName,
            $orderItem->getUnitPrice(),
            $orderItem->getQuantity(),
            (string) $productId
        );

        return $ecommerceOrderProduct;
    }
}
