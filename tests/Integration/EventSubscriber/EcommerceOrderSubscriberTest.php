<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusActiveCampaignPlugin\Integration\EventSubscriber;

use App\Entity\Channel\Channel;
use App\Entity\Customer\Customer;
use App\Entity\Order\Order;
use App\Entity\Order\OrderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Currency\Model\Currency;
use Sylius\Component\Locale\Model\Locale;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\InMemoryTransport;
use Webgriffe\SyliusActiveCampaignPlugin\Message\EcommerceOrder\EcommerceOrderCreate;
use Webgriffe\SyliusActiveCampaignPlugin\Message\EcommerceOrder\EcommerceOrderUpdate;

final class EcommerceOrderSubscriberTest extends AbstractEventDispatcherTest
{
    private OrderRepositoryInterface $orderRepository;

    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->entityManager = self::getContainer()->get('doctrine.orm.entity_manager');
        $this->orderRepository = self::getContainer()->get('sylius.repository.order');
    }

    public function test_that_it_creates_ecommerce_order_on_active_campaign(): void
    {
        $order = $this->createOrder();
        $this->eventDispatcher->dispatch(new ResourceControllerEvent($order), 'sylius.order.post_create');
        /** @var InMemoryTransport $transport */
        $transport = self::getContainer()->get('messenger.transport.main');
        /** @var Envelope[] $messages */
        $messages = $transport->get();
        $this->assertCount(1, $messages);
        $message = $messages[0];
        $this->assertInstanceOf(EcommerceOrderCreate::class, $message->getMessage());
        $this->assertEquals($order->getId(), $message->getMessage()->getOrderId());
    }

    public function test_that_it_updates_ecommerce_order_on_active_campaign(): void
    {
        $order = $this->createOrder();
        $order->setActiveCampaignId(15);
        $this->orderRepository->add($order);
        $this->eventDispatcher->dispatch(new ResourceControllerEvent($order), 'sylius.order.post_update');
        /** @var InMemoryTransport $transport */
        $transport = self::getContainer()->get('messenger.transport.main');
        /** @var Envelope[] $messages */
        $messages = $transport->get();
        $this->assertCount(1, $messages);
        $message = $messages[0];
        $this->assertInstanceOf(EcommerceOrderUpdate::class, $message->getMessage());
        $this->assertEquals($order->getId(), $message->getMessage()->getOrderId());
        $this->assertEquals($order->getActiveCampaignId(), $message->getMessage()->getActiveCampaignId());
    }

    private function createOrder(): OrderInterface
    {
        $locale = new Locale();
        $locale->setCode('en_US');
        $this->entityManager->persist($locale);
        $currency = new Currency();
        $currency->setCode('EUR');
        $this->entityManager->persist($currency);
        $channel = new Channel();
        $channel->setCode('ecommerce');
        $channel->setName('E Commerce');
        $channel->setTaxCalculationStrategy('order_items_based');
        $channel->setDefaultLocale($locale);
        $channel->setBaseCurrency($currency);
        $channel->setActiveCampaignId(43);
        $this->entityManager->persist($channel);

        $customer = new Customer();
        $customer->setEmail('info@domain.org');
        $customer->setActiveCampaignId(432);
        $this->entityManager->persist($customer);

        $order = new Order();
        $order->setCurrencyCode($currency->getCode());
        $order->setLocaleCode($locale->getCode());
        $order->setChannel($channel);
        $order->setCustomer($customer);
        $this->orderRepository->add($order);

        return $order;
    }
}
