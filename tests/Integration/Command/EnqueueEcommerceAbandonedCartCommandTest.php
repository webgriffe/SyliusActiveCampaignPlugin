<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusActiveCampaignPlugin\Integration\Command;

use Fidry\AliceDataFixtures\Persistence\PurgeMode;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\Messenger\Envelope;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Contact\ContactListsSubscriber;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Contact\ContactTagsAdder;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Contact\ContactUpdate;
use Webgriffe\SyliusActiveCampaignPlugin\Message\EcommerceCustomer\EcommerceCustomerCreate;
use Webgriffe\SyliusActiveCampaignPlugin\Message\EcommerceOrder\EcommerceOrderCreate;

final class EnqueueEcommerceAbandonedCartCommandTest extends AbstractCommandTest
{
    private const FIXTURE_BASE_DIR = __DIR__ . '/../../DataFixtures/ORM/resources/Command/EnqueueEcommerceAbandonedCartCommandTest';

    private OrderRepositoryInterface $orderRepository;

    private CustomerRepositoryInterface $customerRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderRepository = self::getContainer()->get('sylius.repository.order');
        $this->customerRepository = self::getContainer()->get('sylius.repository.customer');

        $fixtureLoader = self::getContainer()->get('fidry_alice_data_fixtures.loader.doctrine');
        $fixtureLoader->load([
            self::FIXTURE_BASE_DIR . '/channels.yaml',
            self::FIXTURE_BASE_DIR . '/customers.yaml',
            self::FIXTURE_BASE_DIR . '/orders.yaml',
        ], [], [], PurgeMode::createDeleteMode());
    }

    public function test_that_it_enqueues_ecommerce_abandoned_carts_and_relative_contact(): void
    {
        $commandTester = $this->executeCommand([]);
        self::assertEquals(0, $commandTester->getStatusCode());

        $customerBob = $this->customerRepository->findOneBy(['email' => 'bob@email.com']);
        $bobCart = $this->orderRepository->findOneBy(['customer' => $customerBob->getId()]);
        $transport = self::getContainer()->get('messenger.transport.main');
        /** @var Envelope[] $messages */
        $messages = $transport->get();
        $this->assertCount(5, $messages);

        $message = $messages[0];
        $this->assertInstanceOf(ContactUpdate::class, $message->getMessage());
        $this->assertEquals($bobCart->getCustomer()->getId(), $message->getMessage()->getCustomerId());
        $this->assertEquals($bobCart->getCustomer()->getActiveCampaignId(), $message->getMessage()->getActiveCampaignId());

        $message = $messages[1];
        $this->assertInstanceOf(EcommerceCustomerCreate::class, $message->getMessage());
        $this->assertEquals($bobCart->getCustomer()->getId(), $message->getMessage()->getCustomerId());
        $this->assertEquals($bobCart->getChannel()->getId(), $message->getMessage()->getChannelId());

        $message = $messages[2];
        $this->assertInstanceOf(ContactTagsAdder::class, $message->getMessage());
        $this->assertEquals($bobCart->getCustomer()->getId(), $message->getMessage()->getCustomerId());

        $message = $messages[3];
        $this->assertInstanceOf(ContactListsSubscriber::class, $message->getMessage());
        $this->assertEquals($bobCart->getCustomer()->getId(), $message->getMessage()->getCustomerId());

        $message = $messages[4];
        $this->assertInstanceOf(EcommerceOrderCreate::class, $message->getMessage());
        $this->assertEquals($bobCart->getId(), $message->getMessage()->getOrderId());
        $this->assertTrue($message->getMessage()->isInRealTime());
    }

    protected function getCommandDefinition(): string
    {
        return 'webgriffe.sylius_active_campaign_plugin.command.enqueue_ecommerce_abandoned_cart';
    }
}
