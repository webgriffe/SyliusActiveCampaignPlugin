<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusActiveCampaignPlugin\Mapper;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign\EcommerceOrderProductFactoryInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Generator\ChannelHostnameUrlGeneratorInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Mapper\EcommerceOrderProductMapper;
use Webgriffe\SyliusActiveCampaignPlugin\Mapper\EcommerceOrderProductMapperInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\EcommerceOrderProductInterface;
use Webmozart\Assert\InvalidArgumentException;

class EcommerceOrderProductMapperSpec extends ObjectBehavior
{
    public function let(
        EcommerceOrderProductFactoryInterface $ecommerceOrderProductFactory,
        ChannelHostnameUrlGeneratorInterface $channelHostnameUrlGenerator,
        OrderItemInterface $orderItem,
        ProductInterface $product,
        TaxonInterface $mainTaxon,
        ImageInterface $firstImage,
        OrderInterface $order,
        ChannelInterface $channel,
        LocaleInterface $frenchLocale,
        EcommerceOrderProductInterface $ecommerceOrderProduct
    ): void {
        $ecommerceOrderProductFactory->createNew('Wine bottle', 1200, 2, '432')->willReturn($ecommerceOrderProduct);

        $channelHostnameUrlGenerator->generateForRoute($channel, 'sylius_shop_product_show', ['_locale' => 'it_IT', 'slug' => 'wine-bottle'])->willReturn('https://localhost/products/wine-bottle');
        $channelHostnameUrlGenerator->generateForImage($channel, 'path/wine.png', null)->willReturn('https://domain.org/media/image/path/wine.png');

        $frenchLocale->getCode()->willReturn('fr_FR');

        $channel->getDefaultLocale()->willReturn($frenchLocale);
        $channel->getHostname()->willReturn('domain.org');

        $order->getLocaleCode()->willReturn('it_IT');
        $order->getChannel()->willReturn($channel);

        $orderItem->getOrder()->willReturn($order);
        $orderItem->getProductName()->willReturn('Wine bottle');
        $orderItem->getProduct()->willReturn($product);
        $orderItem->getUnitPrice()->willReturn(1200);
        $orderItem->getQuantity()->willReturn(2);

        $product->getId()->willReturn(432);
        $product->getCode()->willReturn('wine_bottle');
        $product->getSlug()->willReturn('wine-bottle');
        $product->getDescription()->willReturn('Wine bottle of the 1956.');
        $product->getMainTaxon()->willReturn($mainTaxon);
        $product->getImages()->willReturn(new ArrayCollection([$firstImage->getWrappedObject()]));

        $mainTaxon->getName()->willReturn('Wine');

        $firstImage->getPath()->willReturn('path/wine.png');

        $ecommerceOrderProduct->setCategory('Wine');
        $ecommerceOrderProduct->setSku('wine_bottle');
        $ecommerceOrderProduct->setDescription('Wine bottle of the 1956.');
        $ecommerceOrderProduct->setImageUrl('https://domain.org/media/image/path/wine.png');
        $ecommerceOrderProduct->setProductUrl('https://localhost/products/wine-bottle');

        $this->beConstructedWith($ecommerceOrderProductFactory, $channelHostnameUrlGenerator, 'en_US', null, null);
    }

    public function it_is_initializable(): void
    {
        $this->shouldBeAnInstanceOf(EcommerceOrderProductMapper::class);
    }

    public function it_implements_ecommerce_order_product_mapper_interface(): void
    {
        $this->shouldImplement(EcommerceOrderProductMapperInterface::class);
    }

    public function it_throws_if_order_item_order_is_null(OrderItemInterface $orderItem): void
    {
        $orderItem->getOrder()->willReturn(null);
        $this->shouldThrow(new InvalidArgumentException('The order item\'s order should not be null.'))
            ->during('mapFromOrderItem', [$orderItem]);
    }

    public function it_throws_if_order_item_product_name_is_null(OrderItemInterface $orderItem): void
    {
        $orderItem->getProductName()->willReturn(null);
        $this->shouldThrow(new InvalidArgumentException('The order item\'s product name should not be null.'))
            ->during('mapFromOrderItem', [$orderItem]);
    }

    public function it_throws_if_order_item_product_is_null(OrderItemInterface $orderItem): void
    {
        $orderItem->getProduct()->willReturn(null);
        $this->shouldThrow(new InvalidArgumentException('The order item\'s product should not be null.'))
            ->during('mapFromOrderItem', [$orderItem]);
    }

    public function it_throws_if_order_item_product_id_is_null(OrderItemInterface $orderItem, ProductInterface $product): void
    {
        $product->getId()->willReturn(null);
        $this->shouldThrow(new InvalidArgumentException('The order item\'s product id should not be null.'))
            ->during('mapFromOrderItem', [$orderItem]);
    }

    public function it_throws_if_order_item_order_channel_is_null(OrderItemInterface $orderItem, OrderInterface $order): void
    {
        $order->getChannel()->willReturn(null);
        $this->shouldThrow(new InvalidArgumentException('The order\'s channel should not be null.'))
            ->during('mapFromOrderItem', [$orderItem]);
    }

    public function it_maps_ecommerce_order_product_without_category_if_main_taxon_does_not_exist(
        OrderItemInterface $orderItem,
        ProductInterface $product,
        EcommerceOrderProductInterface $ecommerceOrderProduct
    ): void {
        $product->getMainTaxon()->willReturn(null);
        $ecommerceOrderProduct->setCategory('Wine')->shouldNotBeCalled();

        $this->mapFromOrderItem($orderItem)->shouldReturn($ecommerceOrderProduct);
    }

    public function it_maps_ecommerce_order_product_without_image_url_if_products_does_not_have_images(
        OrderItemInterface $orderItem,
        ProductInterface $product,
        EcommerceOrderProductInterface $ecommerceOrderProduct
    ): void {
        $product->getImages()->willReturn(new ArrayCollection());
        $ecommerceOrderProduct->setImageUrl('media/image/path/wine.png')->shouldNotBeCalled();
        $ecommerceOrderProduct->setImageUrl(null)->shouldBeCalledOnce();

        $this->mapFromOrderItem($orderItem)->shouldReturn($ecommerceOrderProduct);
    }

    public function it_maps_ecommerce_order_product_without_image_url_if_products_does_not_have_images_with_specified_type(
        EcommerceOrderProductFactoryInterface $ecommerceOrderProductFactory,
        ChannelHostnameUrlGeneratorInterface $channelHostnameUrlGenerator,
        OrderItemInterface $orderItem,
        ProductInterface $product,
        EcommerceOrderProductInterface $ecommerceOrderProduct
    ): void {
        $this->beConstructedWith($ecommerceOrderProductFactory, $channelHostnameUrlGenerator, 'en_US', 'main');
        $product->getImagesByType('main')->willReturn(new ArrayCollection());
        $product->getImages()->willReturn(new ArrayCollection());
        $ecommerceOrderProduct->setImageUrl('media/image/path/wine.png')->shouldNotBeCalled();
        $ecommerceOrderProduct->setImageUrl(null)->shouldBeCalledOnce();

        $this->mapFromOrderItem($orderItem)->shouldReturn($ecommerceOrderProduct);
    }

    public function it_maps_ecommerce_order_product_with_image_url_from_specified_type(
        EcommerceOrderProductFactoryInterface $ecommerceOrderProductFactory,
        ChannelHostnameUrlGeneratorInterface $channelHostnameUrlGenerator,
        OrderItemInterface $orderItem,
        ProductInterface $product,
        EcommerceOrderProductInterface $ecommerceOrderProduct,
        ImageInterface $typedImage,
        ChannelInterface $channel,
    ): void {
        $this->beConstructedWith($ecommerceOrderProductFactory, $channelHostnameUrlGenerator, 'en_US', 'main');
        $product->getImagesByType('main')->willReturn(new ArrayCollection([$typedImage->getWrappedObject()]));
        $typedImage->getPath()->willReturn('path/main.jpg');
        $channelHostnameUrlGenerator->generateForImage($channel, 'path/main.jpg', null)->willReturn('https://domain.org/media/image/path/main.jpg');
        $ecommerceOrderProduct->setImageUrl('https://domain.org/media/image/path/wine.png')->shouldNotBeCalled();
        $ecommerceOrderProduct->setImageUrl(null)->shouldNotBeCalled();
        $ecommerceOrderProduct->setImageUrl('https://domain.org/media/image/path/main.jpg')->shouldBeCalledOnce();

        $this->mapFromOrderItem($orderItem)->shouldReturn($ecommerceOrderProduct);
    }

    public function it_maps_ecommerce_order_product_without_image_url_from_specified_type_if_image_type_is_a_empty_string(
        EcommerceOrderProductFactoryInterface $ecommerceOrderProductFactory,
        ChannelHostnameUrlGeneratorInterface $channelHostnameUrlGenerator,
        OrderItemInterface $orderItem,
        ProductInterface $product,
        EcommerceOrderProductInterface $ecommerceOrderProduct
    ): void {
        $this->beConstructedWith($ecommerceOrderProductFactory, $channelHostnameUrlGenerator, 'en_US', '');
        $product->getImagesByType('')->shouldNotBeCalled();
        $ecommerceOrderProduct->setImageUrl('https://domain.org/media/image/path/wine.png')->shouldBeCalledOnce();
        $ecommerceOrderProduct->setImageUrl(null)->shouldNotBeCalled();

        $this->mapFromOrderItem($orderItem)->shouldReturn($ecommerceOrderProduct);
    }

    public function it_maps_ecommerce_order_product_with_default_channel_locale_if_not_existing_on_order(
        ChannelHostnameUrlGeneratorInterface $channelHostnameUrlGenerator,
        OrderInterface $order,
        ChannelInterface $channel,
        OrderItemInterface $orderItem,
        EcommerceOrderProductInterface $ecommerceOrderProduct
    ): void {
        $order->getLocaleCode()->willReturn(null);
        $channelHostnameUrlGenerator->generateForRoute($channel, 'sylius_shop_product_show', ['_locale' => 'fr_FR', 'slug' => 'wine-bottle'])->shouldBeCalledOnce()->willReturn('https://localhost/products/wine-bottle');

        $this->mapFromOrderItem($orderItem)->shouldReturn($ecommerceOrderProduct);
    }

    public function it_maps_ecommerce_order_product_with_default_app_locale_if_not_existing_on_order_nor_channel(
        ChannelInterface $channel,
        ChannelHostnameUrlGeneratorInterface $channelHostnameUrlGenerator,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        EcommerceOrderProductInterface $ecommerceOrderProduct
    ): void {
        $order->getLocaleCode()->willReturn(null);
        $channel->getDefaultLocale()->willReturn(null);
        $channelHostnameUrlGenerator->generateForRoute($channel, 'sylius_shop_product_show', ['_locale' => 'en_US', 'slug' => 'wine-bottle'])->shouldBeCalledOnce()->willReturn('https://localhost/products/wine-bottle');

        $this->mapFromOrderItem($orderItem)->shouldReturn($ecommerceOrderProduct);
    }

    public function it_maps_ecommerce_order_product_with_default_app_locale_if_not_existing_code_on_channel_default_locale(
        LocaleInterface $frenchLocale,
        ChannelHostnameUrlGeneratorInterface $channelHostnameUrlGenerator,
        OrderInterface $order,
        ChannelInterface $channel,
        OrderItemInterface $orderItem,
        EcommerceOrderProductInterface $ecommerceOrderProduct
    ): void {
        $order->getLocaleCode()->willReturn(null);
        $frenchLocale->getCode()->willReturn(null);
        $channelHostnameUrlGenerator->generateForRoute($channel, 'sylius_shop_product_show', ['_locale' => 'en_US', 'slug' => 'wine-bottle'])->shouldBeCalledOnce()->willReturn('https://localhost/products/wine-bottle');

        $this->mapFromOrderItem($orderItem)->shouldReturn($ecommerceOrderProduct);
    }

    public function it_maps_ecommerce_order_product_from_order_item(
        OrderItemInterface $orderItem,
        EcommerceOrderProductInterface $ecommerceOrderProduct
    ): void {
        $this->mapFromOrderItem($orderItem)->shouldReturn($ecommerceOrderProduct);
    }
}
