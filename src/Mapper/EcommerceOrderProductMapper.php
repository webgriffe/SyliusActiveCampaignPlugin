<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Mapper;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign\EcommerceOrderProductFactoryInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Generator\ChannelHostnameUrlGeneratorInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\EcommerceOrderProductInterface;
use Webmozart\Assert\Assert;

final class EcommerceOrderProductMapper implements EcommerceOrderProductMapperInterface
{
    public function __construct(
        private EcommerceOrderProductFactoryInterface $ecommerceOrderProductFactory,
        private ChannelHostnameUrlGeneratorInterface $channelHostnameUrlGenerator,
        private string $defaultLocale,
        private ?string $imageType = null,
        private ?string $imageFilter = null,
    ) {
    }

    public function mapFromOrderItem(OrderItemInterface $orderItem): EcommerceOrderProductInterface
    {
        $order = $orderItem->getOrder();
        Assert::isInstanceOf($order, OrderInterface::class, 'The order item\'s order should not be null.');
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
            (string) $productId,
        );
        $mainTaxon = $product->getMainTaxon();
        if ($mainTaxon instanceof TaxonInterface) {
            $ecommerceOrderProduct->setCategory($mainTaxon->getName());
        }
        $ecommerceOrderProduct->setSku($product->getCode());
        $ecommerceOrderProduct->setDescription($product->getDescription());
        $channel = $order->getChannel();
        Assert::isInstanceOf($channel, ChannelInterface::class, 'The order\'s channel should not be null.');
        $ecommerceOrderProduct->setProductUrl($this->getProductUrl($product, $channel, $this->getLocaleCodeFromOrder($order)));
        $ecommerceOrderProduct->setImageUrl($this->getImageUrlFromProductAndChannel($product, $channel));

        return $ecommerceOrderProduct;
    }

    private function getImageUrlFromProductAndChannel(ProductInterface $product, ChannelInterface $channel): ?string
    {
        $productImagePath = $this->getProductImagePath($product);
        if ($productImagePath === null) {
            return null;
        }

        return $this->channelHostnameUrlGenerator->generateForImage(
            $channel,
            $productImagePath,
            $this->imageFilter,
        );
    }

    private function getLocaleCodeFromOrder(OrderInterface $order): string
    {
        $orderLocaleCode = $order->getLocaleCode();
        if ($orderLocaleCode !== null) {
            return $orderLocaleCode;
        }
        $channel = $order->getChannel();
        if ($channel instanceof ChannelInterface && null !== ($channelLocale = $channel->getDefaultLocale())) {
            return $channelLocale->getCode() ?? $this->defaultLocale;
        }

        return $this->defaultLocale;
    }

    private function getProductUrl(ProductInterface $product, ChannelInterface $channel, string $localeCode): string
    {
        return $this->channelHostnameUrlGenerator->generateForRoute(
            $channel,
            'sylius_shop_product_show',
            [
                '_locale' => $localeCode,
                'slug' => $product->getSlug(),
            ],
        );
    }

    private function getProductImagePath(?ProductInterface $product): ?string
    {
        if ($product === null) {
            return null;
        }
        if ($this->imageType !== null && $this->imageType !== '') {
            $images = $product->getImagesByType($this->imageType);
            foreach ($images as $image) {
                return $image->getPath();
            }
        }
        $images = $product->getImages();
        foreach ($images as $image) {
            return $image->getPath();
        }

        return null;
    }
}
