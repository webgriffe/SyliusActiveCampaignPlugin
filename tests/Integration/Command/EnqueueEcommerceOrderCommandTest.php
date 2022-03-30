<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusActiveCampaignPlugin\Integration\Command;

use Fidry\AliceDataFixtures\Persistence\PurgeMode;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\Messenger\Envelope;
use Webgriffe\SyliusActiveCampaignPlugin\Message\EcommerceOrder\EcommerceOrderCreate;
use Webgriffe\SyliusActiveCampaignPlugin\Message\EcommerceOrder\EcommerceOrderRemove;
use Webgriffe\SyliusActiveCampaignPlugin\Message\EcommerceOrder\EcommerceOrderUpdate;

// TODO: This test should be improved with testing an already existing order in ActiveCampaign, but there is the need to found a way to "force"
// id on alice data fixture.
final class EnqueueEcommerceOrderCommandTest extends AbstractCommandTest
{
    private const FIXTURE_BASE_DIR = __DIR__ . '/../../DataFixtures/ORM/resources/Command/EnqueueEcommerceOrderCommandTest';

    private OrderRepositoryInterface $orderRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderRepository = self::getContainer()->get('sylius.repository.order');

        $fixtureLoader = self::getContainer()->get('fidry_alice_data_fixtures.loader.doctrine');
        $fixtureLoader->load([
            self::FIXTURE_BASE_DIR . '/channels.yaml',
            self::FIXTURE_BASE_DIR . '/customers.yaml',
            self::FIXTURE_BASE_DIR . '/orders.yaml',
        ], [], [], PurgeMode::createDeleteMode());
    }

    public function test_that_it_enqueues_ecommerce_order(): void
    {
        $order = $this->orderRepository->findOneBy(['number' => '0001']);
        $commandTester = $this->executeCommand([
            'order-id' => $order->getId(),
        ], []);
        self::assertEquals(0, $commandTester->getStatusCode());

        $transport = self::getContainer()->get('messenger.transport.main');
        /** @var Envelope[] $messages */
        $messages = $transport->get();
        $this->assertCount(1, $messages);
        $message = $messages[0];
        $this->assertInstanceOf(EcommerceOrderCreate::class, $message->getMessage());
        $this->assertEquals($order->getId(), $message->getMessage()->getOrderId());
    }

    public function test_that_it_enqueues_ecommerce_order_interactively(): void
    {
        $order = $this->orderRepository->findOneBy(['number' => '0001']);
        self::assertNotNull($order->getId());
        $commandTester = $this->executeCommand([], [
            $order->getId(),
        ]);
        self::assertEquals(0, $commandTester->getStatusCode());

        $transport = self::getContainer()->get('messenger.transport.main');
        /** @var Envelope[] $messages */
        $messages = $transport->get();
        $this->assertCount(1, $messages);
        $message = $messages[0];
        $this->assertInstanceOf(EcommerceOrderCreate::class, $message->getMessage());
        $this->assertEquals($order->getId(), $message->getMessage()->getOrderId());
    }

    public function test_that_it_enqueues_all_ecommerce_orders(): void
    {
        $commandTester = $this->executeCommand([
            '--all' => true,
        ]);
        self::assertEquals(0, $commandTester->getStatusCode());

        $order0001 = $this->orderRepository->findOneBy(['number' => '0001']);
        $order0002 = $this->orderRepository->findOneBy(['number' => '0002']);
        $order0003 = $this->orderRepository->findOneBy(['number' => '0003']);
        $transport = self::getContainer()->get('messenger.transport.main');
        /** @var Envelope[] $messages */
        $messages = $transport->get();
        $this->assertCount(3, $messages);
        $message = $messages[0];
        $this->assertInstanceOf(EcommerceOrderCreate::class, $message->getMessage());
        $this->assertEquals($order0001->getId(), $message->getMessage()->getOrderId());

        $message = $messages[1];
        $this->assertInstanceOf(EcommerceOrderUpdate::class, $message->getMessage());
        $this->assertEquals($order0002->getId(), $message->getMessage()->getOrderId());
        $this->assertEquals($order0002->getActiveCampaignId(), $message->getMessage()->getActiveCampaignId());

        $message = $messages[2];
        $this->assertInstanceOf(EcommerceOrderRemove::class, $message->getMessage());
        $this->assertEquals(6, $message->getMessage()->getActiveCampaignId());
        $this->assertNull($order0003->getActiveCampaignId());
    }

    protected function getCommandDefinition(): string
    {
        return 'webgriffe.sylius_active_campaign_plugin.command.enqueue_ecommerce_order';
    }
}
