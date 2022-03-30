<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign;

use PhpSpec\ObjectBehavior;
use Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign\EcommerceOrderProductFactory;
use Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign\EcommerceOrderProductFactoryInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\EcommerceOrderProduct;

class EcommerceOrderProductFactorySpec extends ObjectBehavior
{
    public function let(): void
    {
        $this->beConstructedWith();
    }

    public function it_is_initializable(): void
    {
        $this->shouldBeAnInstanceOf(EcommerceOrderProductFactory::class);
    }

    public function it_implements_active_campaign_ecommerce_order_product_factory_interface(): void
    {
        $this->shouldImplement(EcommerceOrderProductFactoryInterface::class);
    }

    public function it_should_returns_an_active_campaign_ecommerce_order_product_instance(): void
    {
        $this->createNew(
            'Wine Bottle',
            1000,
            2,
            '435'
        )->shouldReturnAnInstanceOf(EcommerceOrderProduct::class);
    }
}
