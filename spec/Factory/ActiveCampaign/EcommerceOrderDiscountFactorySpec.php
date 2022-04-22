<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign;

use PhpSpec\ObjectBehavior;
use Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign\EcommerceOrderDiscountFactory;
use Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign\EcommerceOrderDiscountFactoryInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\EcommerceOrderDiscount;

class EcommerceOrderDiscountFactorySpec extends ObjectBehavior
{
    public function let(): void
    {
        $this->beConstructedWith(EcommerceOrderDiscount::class);
    }

    public function it_is_initializable(): void
    {
        $this->shouldBeAnInstanceOf(EcommerceOrderDiscountFactory::class);
    }

    public function it_implements_active_campaign_ecommerce_order_discount_factory_interface(): void
    {
        $this->shouldImplement(EcommerceOrderDiscountFactoryInterface::class);
    }

    public function it_should_returns_an_active_campaign_ecommerce_order_discount_instance(): void
    {
        $this->createNew()->shouldReturnAnInstanceOf(EcommerceOrderDiscount::class);
    }
}
