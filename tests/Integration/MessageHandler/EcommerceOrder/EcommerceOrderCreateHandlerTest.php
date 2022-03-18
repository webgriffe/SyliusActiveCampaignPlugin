<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusActiveCampaignPlugin\Integration\MessageHandler\EcommerceOrder;

use App\Entity\Channel\Channel;
use App\Entity\Customer\Customer;
use App\Entity\Order\Order;
use App\Entity\Order\OrderInterface;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Currency\Model\Currency;
use Sylius\Component\Locale\Model\Locale;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Webgriffe\SyliusActiveCampaignPlugin\Stub\ActiveCampaignEcommerceOrderClientStub;
use Webgriffe\SyliusActiveCampaignPlugin\Message\EcommerceOrder\EcommerceOrderCreate;
use Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\EcommerceOrder\EcommerceOrderCreateHandler;

final class EcommerceOrderCreateHandlerTest extends KernelTestCase
{
    private OrderRepositoryInterface $orderRepository;

    private EcommerceOrderCreateHandler $ecommerceOrderCreateHandler;

    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->entityManager = self::getContainer()->get('doctrine.orm.entity_manager');
        $purger = new ORMPurger($this->entityManager);
        $purger->purge();
        $this->orderRepository = self::getContainer()->get('sylius.repository.order');
        $this->ecommerceOrderCreateHandler = new EcommerceOrderCreateHandler(
            self::getContainer()->get('webgriffe.sylius_active_campaign_plugin.mapper.ecommerce_order'),
            new ActiveCampaignEcommerceOrderClientStub(),
            $this->orderRepository
        );
    }

    public function test_that_it_creates_ecommerce_order_on_active_campaign(): void
    {
        $order = $this->createOrder();
        $this->ecommerceOrderCreateHandler->__invoke(new EcommerceOrderCreate($order->getId(), true));

        /** @var OrderInterface|null $order */
        $order = $this->orderRepository->find($order->getId());
        $this->assertEquals(222, $order->getActiveCampaignId());
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
        $channel->setActiveCampaignId(1);
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
