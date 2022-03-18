<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Mapper;

use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Symfony\Component\Routing\RouterInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign\EcommerceOrderProductFactoryInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\EcommerceOrderProductInterface;
use Webmozart\Assert\Assert;

final class EcommerceOrderProductMapper implements EcommerceOrderProductMapperInterface
{
    public function __construct(
        private EcommerceOrderProductFactoryInterface $ecommerceOrderProductFactory,
        private RouterInterface $router
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
        $mainTaxon = $product->getMainTaxon();
        if ($mainTaxon instanceof TaxonInterface) {
            $ecommerceOrderProduct->setCategory($mainTaxon->getName());
        }
        $ecommerceOrderProduct->setSku($product->getCode());
        $ecommerceOrderProduct->setDescription($product->getDescription());
        $firstImage = $product->getImages()->first();
        if ($firstImage instanceof ImageInterface) {
            $ecommerceOrderProduct->setImageUrl($firstImage->getPath());
        }
        $ecommerceOrderProduct->setProductUrl($this->router->generate('sylius_shop_product_show', ['slug' => $product->getSlug()]));

        return $ecommerceOrderProduct;
    }
}
