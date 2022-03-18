<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusActiveCampaignPlugin\Mapper;

use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Symfony\Component\Routing\RouterInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign\EcommerceOrderProductFactoryInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Mapper\EcommerceOrderProductMapper;
use PhpSpec\ObjectBehavior;
use Webgriffe\SyliusActiveCampaignPlugin\Mapper\EcommerceOrderProductMapperInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\EcommerceOrderProductInterface;
use Webmozart\Assert\InvalidArgumentException;

class EcommerceOrderProductMapperSpec extends ObjectBehavior
{
    public function let(
        EcommerceOrderProductFactoryInterface $ecommerceOrderProductFactory,
        RouterInterface $router,
        OrderItemInterface $orderItem,
        ProductInterface $product,
        TaxonInterface $mainTaxon,
        ImageInterface $firstImage,
        EcommerceOrderProductInterface $ecommerceOrderProduct
    ): void {
        $ecommerceOrderProductFactory->createNew('Wine bottle', 1200, 2, '432')->willReturn($ecommerceOrderProduct);

        $router->generate('sylius_shop_product_show', ['slug' => 'wine-bottle'])->willReturn('https://localhost/products/wine-bottle');

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
        $ecommerceOrderProduct->setImageUrl('path/wine.png');
        $ecommerceOrderProduct->setProductUrl('https://localhost/products/wine-bottle');

        $this->beConstructedWith($ecommerceOrderProductFactory, $router);
    }

    public function it_is_initializable(): void
    {
        $this->shouldBeAnInstanceOf(EcommerceOrderProductMapper::class);
    }

    public function it_implements_ecommerce_order_product_mapper_interface(): void
    {
        $this->shouldImplement(EcommerceOrderProductMapperInterface::class);
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

    public function it_maps_ecommerce_order_product_without_category_if_main_taxon_does_not_exist(
        OrderItemInterface $orderItem,
        ProductInterface $product,
        EcommerceOrderProductFactoryInterface $ecommerceOrderProductFactory,
        EcommerceOrderProductInterface $ecommerceOrderProduct
    ): void {
        $product->getMainTaxon()->willReturn(null);
        $ecommerceOrderProduct->setCategory('Wine')->shouldNotBeCalled();

        $this->mapFromOrderItem($orderItem)->shouldReturn($ecommerceOrderProduct);
    }

    public function it_maps_ecommerce_order_product_without_image_url_if_products_does_not_have_images(
        OrderItemInterface $orderItem,
        ProductInterface $product,
        EcommerceOrderProductFactoryInterface $ecommerceOrderProductFactory,
        EcommerceOrderProductInterface $ecommerceOrderProduct
    ): void {
        $product->getImages()->willReturn(new ArrayCollection());
        $ecommerceOrderProduct->setImageUrl('path/wine.png')->shouldNotBeCalled();

        $this->mapFromOrderItem($orderItem)->shouldReturn($ecommerceOrderProduct);
    }

    public function it_maps_ecommerce_order_product_from_order_item(
        OrderItemInterface $orderItem,
        EcommerceOrderProductInterface $ecommerceOrderProduct
    ): void {
        $this->mapFromOrderItem($orderItem)->shouldReturn($ecommerceOrderProduct);
    }
}
