<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusActiveCampaignPlugin\Integration\EventSubscriber;

use Fidry\AliceDataFixtures\Persistence\PurgeMode;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\InMemoryTransport;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Contact\ContactCreate;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Contact\ContactListsSubscriber;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Contact\ContactTagsAdder;
use Webgriffe\SyliusActiveCampaignPlugin\Message\EcommerceCustomer\EcommerceCustomerCreate;
use Webgriffe\SyliusActiveCampaignPlugin\Message\EcommerceOrder\EcommerceOrderCreate;
use Webgriffe\SyliusActiveCampaignPlugin\Message\EcommerceOrder\EcommerceOrderRemove;
use Webgriffe\SyliusActiveCampaignPlugin\Message\EcommerceOrder\EcommerceOrderUpdate;

final class OrderSubscriberTest extends AbstractEventDispatcherTest
{
    private const FIXTURE_BASE_DIR = __DIR__ . '/../../DataFixtures/ORM/resources/EventSubscriber/OrderSubscriberTest';

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

    public function test_that_it_creates_contact_and_relatives_and_ecommerce_order_on_active_campaign_on_order_creation(): void
    {
        $order = $this->orderRepository->findOneBy(['number' => '0001']);
        $this->eventDispatcher->dispatch(new ResourceControllerEvent($order), 'sylius.order.post_create');
        /** @var InMemoryTransport $transport */
        $transport = self::getContainer()->get('messenger.transport.main');
        /** @var Envelope[] $messages */
        $messages = $transport->get();
        $this->assertCount(5, $messages);

        $message = $messages[0];
        $this->assertInstanceOf(ContactCreate::class, $message->getMessage());
        $this->assertEquals($order->getCustomer()->getId(), $message->getMessage()->getCustomerId());

        $message = $messages[1];
        $this->assertInstanceOf(EcommerceCustomerCreate::class, $message->getMessage());
        $this->assertEquals($order->getCustomer()->getId(), $message->getMessage()->getCustomerId());
        $this->assertEquals($order->getChannel()->getId(), $message->getMessage()->getChannelId());

        $message = $messages[2];
        $this->assertInstanceOf(ContactTagsAdder::class, $message->getMessage());
        $this->assertEquals($order->getCustomer()->getId(), $message->getMessage()->getCustomerId());

        $message = $messages[3];
        $this->assertInstanceOf(ContactListsSubscriber::class, $message->getMessage());
        $this->assertEquals($order->getCustomer()->getId(), $message->getMessage()->getCustomerId());

        $message = $messages[4];
        $this->assertInstanceOf(EcommerceOrderCreate::class, $message->getMessage());
        $this->assertEquals($order->getId(), $message->getMessage()->getOrderId());
        $this->assertFalse($message->getMessage()->isInRealTime());
    }

    public function test_that_it_updates_contact_and_relatives_and_ecommerce_order_on_active_campaign_after_order_update(): void
    {
        $order = $this->orderRepository->findOneBy(['number' => '0002']);
        $this->eventDispatcher->dispatch(new ResourceControllerEvent($order), 'sylius.order.post_update');
        /** @var InMemoryTransport $transport */
        $transport = self::getContainer()->get('messenger.transport.main');
        /** @var Envelope[] $messages */
        $messages = $transport->get();
        $this->assertCount(5, $messages);

        $message = $messages[0];
        $this->assertInstanceOf(ContactCreate::class, $message->getMessage());
        $this->assertEquals($order->getCustomer()->getId(), $message->getMessage()->getCustomerId());

        $message = $messages[1];
        $this->assertInstanceOf(EcommerceCustomerCreate::class, $message->getMessage());
        $this->assertEquals($order->getCustomer()->getId(), $message->getMessage()->getCustomerId());
        $this->assertEquals($order->getChannel()->getId(), $message->getMessage()->getChannelId());

        $message = $messages[2];
        $this->assertInstanceOf(ContactTagsAdder::class, $message->getMessage());
        $this->assertEquals($order->getCustomer()->getId(), $message->getMessage()->getCustomerId());

        $message = $messages[3];
        $this->assertInstanceOf(ContactListsSubscriber::class, $message->getMessage());
        $this->assertEquals($order->getCustomer()->getId(), $message->getMessage()->getCustomerId());

        $message = $messages[4];
        $this->assertInstanceOf(EcommerceOrderUpdate::class, $message->getMessage());
        $this->assertEquals($order->getId(), $message->getMessage()->getOrderId());
        $this->assertEquals($order->getActiveCampaignId(), $message->getMessage()->getActiveCampaignId());
        $this->assertFalse($message->getMessage()->isInRealTime());
    }

    public function test_that_it_removes_ecommerce_order_on_active_campaign(): void
    {
        $order = $this->orderRepository->findOneBy(['number' => '0002']);
        $this->eventDispatcher->dispatch(new ResourceControllerEvent($order), 'sylius.order.post_delete');
        /** @var InMemoryTransport $transport */
        $transport = self::getContainer()->get('messenger.transport.main');
        /** @var Envelope[] $messages */
        $messages = $transport->get();
        $this->assertCount(1, $messages);
        $message = $messages[0];
        $this->assertInstanceOf(EcommerceOrderRemove::class, $message->getMessage());
        $this->assertEquals($order->getActiveCampaignId(), $message->getMessage()->getActiveCampaignId());
    }

    public function test_that_it_creates_contact_and_relatives_and_ecommerce_order_on_active_campaign_after_checkout_complete(): void
    {
        $newOrder = $this->orderRepository->findOneBy(['number' => '0001']);
        $this->eventDispatcher->dispatch(new ResourceControllerEvent($newOrder), 'sylius.order.post_complete');
        /** @var InMemoryTransport $transport */
        $transport = self::getContainer()->get('messenger.transport.main');
        /** @var Envelope[] $messages */
        $messages = $transport->get();
        $this->assertCount(5, $messages);

        $message = $messages[0];
        $this->assertInstanceOf(ContactCreate::class, $message->getMessage());
        $this->assertEquals($newOrder->getCustomer()->getId(), $message->getMessage()->getCustomerId());

        $message = $messages[1];
        $this->assertInstanceOf(EcommerceCustomerCreate::class, $message->getMessage());
        $this->assertEquals($newOrder->getCustomer()->getId(), $message->getMessage()->getCustomerId());
        $this->assertEquals($newOrder->getChannel()->getId(), $message->getMessage()->getChannelId());

        $message = $messages[2];
        $this->assertInstanceOf(ContactTagsAdder::class, $message->getMessage());
        $this->assertEquals($newOrder->getCustomer()->getId(), $message->getMessage()->getCustomerId());

        $message = $messages[3];
        $this->assertInstanceOf(ContactListsSubscriber::class, $message->getMessage());
        $this->assertEquals($newOrder->getCustomer()->getId(), $message->getMessage()->getCustomerId());

        $message = $messages[4];
        $this->assertInstanceOf(EcommerceOrderCreate::class, $message->getMessage());
        $this->assertEquals($newOrder->getId(), $message->getMessage()->getOrderId());
        $this->assertTrue($message->getMessage()->isInRealTime());
    }

    public function test_that_it_updates_contact_and_relatives_and_ecommerce_order_on_active_campaign_after_checkout_complete(): void
    {
        $oldOrder = $this->orderRepository->findOneBy(['number' => '0002']);
        $this->eventDispatcher->dispatch(new ResourceControllerEvent($oldOrder), 'sylius.order.post_complete');
        /** @var InMemoryTransport $transport */
        $transport = self::getContainer()->get('messenger.transport.main');
        /** @var Envelope[] $messages */
        $messages = $transport->get();

        $this->assertCount(5, $messages);

        $message = $messages[0];
        $this->assertInstanceOf(ContactCreate::class, $message->getMessage());
        $this->assertEquals($oldOrder->getCustomer()->getId(), $message->getMessage()->getCustomerId());

        $message = $messages[1];
        $this->assertInstanceOf(EcommerceCustomerCreate::class, $message->getMessage());
        $this->assertEquals($oldOrder->getCustomer()->getId(), $message->getMessage()->getCustomerId());
        $this->assertEquals($oldOrder->getChannel()->getId(), $message->getMessage()->getChannelId());

        $message = $messages[2];
        $this->assertInstanceOf(ContactTagsAdder::class, $message->getMessage());
        $this->assertEquals($oldOrder->getCustomer()->getId(), $message->getMessage()->getCustomerId());

        $message = $messages[3];
        $this->assertInstanceOf(ContactListsSubscriber::class, $message->getMessage());
        $this->assertEquals($oldOrder->getCustomer()->getId(), $message->getMessage()->getCustomerId());

        $message = $messages[4];
        $this->assertInstanceOf(EcommerceOrderUpdate::class, $message->getMessage());
        $this->assertEquals($oldOrder->getId(), $message->getMessage()->getOrderId());
        $this->assertTrue($message->getMessage()->isInRealTime());
    }
}
