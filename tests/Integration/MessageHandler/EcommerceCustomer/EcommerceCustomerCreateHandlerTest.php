<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusActiveCampaignPlugin\Integration\MessageHandler\EcommerceCustomer;

use Fidry\AliceDataFixtures\Persistence\PurgeMode;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Webgriffe\SyliusActiveCampaignPlugin\Stub\ActiveCampaignEcommerceCustomerClientStub;
use Webgriffe\SyliusActiveCampaignPlugin\Message\EcommerceCustomer\EcommerceCustomerCreate;
use Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\EcommerceCustomer\EcommerceCustomerCreateHandler;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ChannelCustomerInterface;

final class EcommerceCustomerCreateHandlerTest extends KernelTestCase
{
    private const FIXTURE_BASE_DIR = __DIR__ . '/../../../DataFixtures/ORM/resources/MessageHandler/EcommerceCustomerCreateHandlerTest';

    private CustomerRepositoryInterface $customerRepository;

    private RepositoryInterface $channelCustomerRepository;

    private ChannelRepositoryInterface $channelRepository;

    private EcommerceCustomerCreateHandler $ecommerceCustomerCreateHandler;

    protected function setUp(): void
    {
        parent::setUp();
        $this->channelCustomerRepository = self::getContainer()->get('webgriffe_sylius_active_campaign.repository.channel_customer');
        $this->customerRepository = self::getContainer()->get('sylius.repository.customer');
        $this->channelRepository = self::getContainer()->get('sylius.repository.channel');

        $fixtureLoader = self::getContainer()->get('fidry_alice_data_fixtures.loader.doctrine');
        $fixtureLoader->load([
            self::FIXTURE_BASE_DIR . '/channels.yaml',
            self::FIXTURE_BASE_DIR . '/customers.yaml',
        ], [], [], PurgeMode::createDeleteMode());

        // todo: it would be great to have only one stub for all resources
        $this->ecommerceCustomerCreateHandler = new EcommerceCustomerCreateHandler(
            self::getContainer()->get('webgriffe.sylius_active_campaign_plugin.mapper.ecommerce_customer'),
            new ActiveCampaignEcommerceCustomerClientStub(),
            $this->customerRepository,
            $this->channelRepository,
            self::getContainer()->get('webgriffe_sylius_active_campaign.factory.channel_customer'),
            self::getContainer()->get('doctrine.orm.entity_manager'),
        );
    }

    public function test_that_it_creates_ecommerce_customer_on_active_campaign(): void
    {
        $channel = $this->channelRepository->findOneBy(['code' => 'fashion_shop']);
        $customer = $this->customerRepository->findOneBy(['email' => 'jim@email.com']);
        $this->ecommerceCustomerCreateHandler->__invoke(new EcommerceCustomerCreate($customer->getId(), $channel->getId()));

        /** @var ChannelCustomerInterface $channelCustomer */
        $channelCustomer = $this->channelCustomerRepository->findOneBy(['customer' => $customer, 'channel' => $channel]);
        /** @var CustomerInterface&ActiveCampaignAwareInterface $customer */
        $customer = $this->customerRepository->find($customer->getId());
        $this->assertNull($customer->getActiveCampaignId());
        $this->assertEquals(3423, $channelCustomer->getActiveCampaignId());
    }
}
