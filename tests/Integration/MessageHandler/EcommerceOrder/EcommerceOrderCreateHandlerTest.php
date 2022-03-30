<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusActiveCampaignPlugin\Integration\MessageHandler\EcommerceOrder;

use App\Entity\Order\OrderInterface;
use Fidry\AliceDataFixtures\Persistence\PurgeMode;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Webgriffe\SyliusActiveCampaignPlugin\Message\EcommerceOrder\EcommerceOrderCreate;
use Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\EcommerceOrder\EcommerceOrderCreateHandler;

final class EcommerceOrderCreateHandlerTest extends KernelTestCase
{
    private const FIXTURE_BASE_DIR = __DIR__ . '/../../../DataFixtures/ORM/resources/MessageHandler/EcommerceOrderCreateHandlerTest';

    private OrderRepositoryInterface $orderRepository;

    private EcommerceOrderCreateHandler $ecommerceOrderCreateHandler;

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

        $this->ecommerceOrderCreateHandler = new EcommerceOrderCreateHandler(
            self::getContainer()->get('webgriffe.sylius_active_campaign_plugin.mapper.ecommerce_order'),
            self::getContainer()->get('webgriffe.sylius_active_campaign_plugin.client_stub.active_campaign.ecommerce_order'),
            $this->orderRepository
        );
    }

    public function test_that_it_creates_ecommerce_order_on_active_campaign(): void
    {
        $order = $this->orderRepository->findOneBy(['number' => '0001']);
        $this->ecommerceOrderCreateHandler->__invoke(new EcommerceOrderCreate($order->getId(), true));

        /** @var OrderInterface|null $order */
        $order = $this->orderRepository->find($order->getId());
        $this->assertEquals(222, $order->getActiveCampaignId());
    }
}
