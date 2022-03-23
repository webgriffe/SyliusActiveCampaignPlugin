<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign;

use PhpSpec\ObjectBehavior;
use Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign\EcommerceCustomerFactory;
use Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign\EcommerceCustomerFactoryInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\EcommerceCustomerInterface;

class EcommerceCustomerFactorySpec extends ObjectBehavior
{
    public function let(): void
    {
        $this->beConstructedWith();
    }

    public function it_is_initializable(): void
    {
        $this->shouldBeAnInstanceOf(EcommerceCustomerFactory::class);
    }

    public function it_implements_active_campaign_ecommerce_customer_factory_interface(): void
    {
        $this->shouldImplement(EcommerceCustomerFactoryInterface::class);
    }

    public function it_should_returns_an_active_campaign_ecommerce_customer_instance(): void
    {
        $this->createNew('info@domain.org', '10', '512')->shouldReturnAnInstanceOf(EcommerceCustomerInterface::class);
    }

    public function it_should_returns_an_active_campaign_ecommerce_customer_with_data(): void
    {
        $result = $this->createNew('info@domain.org', '10', '512');
        $result->getEmail()->shouldReturn('info@domain.org');
        $result->getConnectionId()->shouldReturn('10');
        $result->getExternalId()->shouldReturn('512');
    }
}
