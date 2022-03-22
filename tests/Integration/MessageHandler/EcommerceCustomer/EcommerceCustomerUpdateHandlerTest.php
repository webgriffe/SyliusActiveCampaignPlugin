<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusActiveCampaignPlugin\Integration\MessageHandler\EcommerceCustomer;

use Fidry\AliceDataFixtures\Persistence\PurgeMode;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Webgriffe\SyliusActiveCampaignPlugin\Stub\ActiveCampaignEcommerceCustomerClientStub;
use Webgriffe\SyliusActiveCampaignPlugin\Message\EcommerceCustomer\EcommerceCustomerUpdate;
use Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\EcommerceCustomer\EcommerceCustomerUpdateHandler;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface;

final class EcommerceCustomerUpdateHandlerTest extends KernelTestCase
{
    private const FIXTURE_BASE_DIR = __DIR__ . '/../../../DataFixtures/ORM/resources/MessageHandler/EcommerceCustomerUpdateHandlerTest';

    private CustomerRepositoryInterface $customerRepository;

    private ChannelRepositoryInterface $channelRepository;

    private EcommerceCustomerUpdateHandler $ecommerceCustomerUpdateHandler;

    protected function setUp(): void
    {
        parent::setUp();
        $this->customerRepository = self::getContainer()->get('sylius.repository.customer');
        $this->channelRepository = self::getContainer()->get('sylius.repository.channel');

        $fixtureLoader = self::getContainer()->get('fidry_alice_data_fixtures.loader.doctrine');
        $fixtureLoader->load([
            self::FIXTURE_BASE_DIR . '/channels.yaml',
            self::FIXTURE_BASE_DIR . '/customers.yaml',
        ], [], [], PurgeMode::createDeleteMode());

        // todo: it would be great to have only one stub for all resources
        $this->ecommerceCustomerUpdateHandler = new EcommerceCustomerUpdateHandler(
            self::getContainer()->get('webgriffe.sylius_active_campaign_plugin.mapper.ecommerce_customer'),
            new ActiveCampaignEcommerceCustomerClientStub(),
            $this->customerRepository,
            $this->channelRepository
        );
    }

    public function test_that_it_update_ecommerce_customer_on_active_campaign(): void
    {
        $channel = $this->channelRepository->findOneBy(['code' => 'fashion_shop']);
        $customer = $this->customerRepository->findOneBy(['email' => 'jim@email.com']);
        $this->ecommerceCustomerUpdateHandler->__invoke(new EcommerceCustomerUpdate($customer->getId(), $customer->getActiveCampaignId(), $channel->getId()));

        /** @var CustomerInterface&ActiveCampaignAwareInterface $customer */
        $customer = $this->customerRepository->find($customer->getId());
        $this->assertEquals(1234, $customer->getActiveCampaignId());
    }
}
