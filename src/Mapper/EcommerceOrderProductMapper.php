<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Mapper;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Symfony\Component\Asset\UrlPackage;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;
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
        private string $scheme = 'http',
        private ?string $imageType = null
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
            (string) $productId
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

    private function getImageUrlFromPathAndChannel(string $path, ChannelInterface $channel): string
    {
        $hostname = $channel->getHostname();
        Assert::notNull($hostname, 'The channel\'s hostname should not be null.');
        // TODO: is there any better way to handle this? Especially the media/image directory
        $urlPackage = new UrlPackage(
            $this->scheme . '://' . $hostname . (str_ends_with($hostname, '/') ? '' : '/') . 'media/image',
            new EmptyVersionStrategy()
        );

        return $urlPackage->getUrl($path);
    }

    private function getImageUrlFromProductAndChannel(ProductInterface $product, ChannelInterface $channel): ?string
    {
        if ($this->imageType === null || $this->imageType === '') {
            $firstImage = $product->getImages()->first();
            if (!$firstImage instanceof ImageInterface || ($path = $firstImage->getPath()) === null) {
                return null;
            }

            return $this->getImageUrlFromPathAndChannel($path, $channel);
        }
        $imageForType = $product->getImagesByType($this->imageType)->first();
        if (!$imageForType instanceof ImageInterface || ($path = $imageForType->getPath()) === null) {
            return null;
        }

        return $this->getImageUrlFromPathAndChannel($path, $channel);
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
        return $this->channelHostnameUrlGenerator->generate(
            $channel,
            'sylius_shop_product_show',
            [
                '_locale' => $localeCode,
                'slug' => $product->getSlug(),
            ]
        );
    }
}
