<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign;

use DateTime;
use Webgriffe\SyliusActiveCampaignPlugin\Exception\EcommerceOrderAbandonedDateRequiredException;
use Webgriffe\SyliusActiveCampaignPlugin\Exception\EcommerceOrderExternalIdNotValidException;
use Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign\EcommerceOrderFactory;
use PhpSpec\ObjectBehavior;
use Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign\EcommerceOrderFactoryInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\EcommerceOrderInterface;

class EcommerceOrderFactorySpec extends ObjectBehavior
{
    public function let(): void
    {
        $this->beConstructedWith();
    }

    public function it_is_initializable(): void
    {
        $this->shouldBeAnInstanceOf(EcommerceOrderFactory::class);
    }

    public function it_implements_active_campaign_ecommerce_order_factory_interface(): void
    {
        $this->shouldImplement(EcommerceOrderFactoryInterface::class);
    }

    public function it_throws_if_neither_external_id_nor_external_checkout_id_is_given(): void
    {
        $this->shouldThrow(
            new EcommerceOrderExternalIdNotValidException('One property between "externalId" and "externalCheckoutId" must be valued.')
        )->during('createNew', [
            'info@domain.org',
            10,
            512,
            'EUR',
            53212,
            new DateTime('2022-03-18 10:52'),
            null,
            null,
            null
        ]);
    }

    public function it_throws_if_abandoned_date_is_not_given_when_external_checkout_id_is_not_null(): void
    {
        $this->shouldThrow(
            new EcommerceOrderAbandonedDateRequiredException('The "abandonedDate" property can not be null if the "externalCheckoutId" is not null.')
        )->during('createNew', [
            'info@domain.org',
            10,
            512,
            'EUR',
            53212,
            new DateTime('2022-03-18 10:52'),
            null,
            '321',
            null
        ]);
    }

    public function it_should_returns_an_active_campaign_ecommerce_order_instance(): void
    {
        $this->createNew(
            'info@domain.org',
            10,
            512,
            'EUR',
            53212,
            new DateTime('2022-03-18 10:52'),
            '12',
            null,
            null
        )->shouldReturnAnInstanceOf(EcommerceOrderInterface::class);
    }

    public function it_could_returns_an_active_campaign_ecommerce_order_instance_with_external_id(): void
    {
        $ecommerceOrder = $this->createNew(
            'info@domain.org',
            10,
            512,
            'EUR',
            53212,
            new DateTime('2022-03-18 10:52'),
            '12',
            null,
            null
        );

        $ecommerceOrder->getExternalId()->shouldReturn('12');
        $ecommerceOrder->getExternalCheckoutId()->shouldReturn(null);
    }

    public function it_could_returns_an_active_campaign_ecommerce_order_instance_with_external_checkout_id(): void
    {
        $ecommerceOrder = $this->createNew(
            'info@domain.org',
            10,
            512,
            'EUR',
            53212,
            new DateTime('2022-03-12 19:05'),
            null,
            '321',
            new DateTime('2022-03-18 10:52')
        );

        $ecommerceOrder->getExternalId()->shouldReturn(null);
        $ecommerceOrder->getExternalCheckoutId()->shouldReturn('321');
    }
}
