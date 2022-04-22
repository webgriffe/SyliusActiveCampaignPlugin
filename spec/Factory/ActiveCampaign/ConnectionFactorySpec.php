<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign;

use Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign\ConnectionFactory;
use PhpSpec\ObjectBehavior;
use Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign\ConnectionFactoryInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\Connection;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ConnectionInterface;

class ConnectionFactorySpec extends ObjectBehavior
{
    public function let(): void
    {
        $this->beConstructedWith(Connection::class);
    }

    public function it_is_initializable(): void
    {
        $this->shouldBeAnInstanceOf(ConnectionFactory::class);
    }

    public function it_implements_active_campaign_connection_factory_interface(): void
    {
        $this->shouldImplement(ConnectionFactoryInterface::class);
    }

    public function it_should_returns_an_active_campaign_connection_instance(): void
    {
        $this->createNew('sylius', 'ecommerce', 'eCommerce')->shouldReturnAnInstanceOf(ConnectionInterface::class);
    }

    public function it_should_returns_an_active_campaign_connection_with_name(): void
    {
        $this->createNew('sylius', 'ecommerce', 'eCommerce')->getName()->shouldReturn('eCommerce');
    }
}
