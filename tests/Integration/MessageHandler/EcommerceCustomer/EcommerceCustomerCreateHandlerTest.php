<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusActiveCampaignPlugin\Integration\MessageHandler\EcommerceCustomer;

use App\Entity\Channel\Channel;
use App\Entity\Channel\ChannelInterface;
use App\Entity\Customer\Customer;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Currency\Model\Currency;
use Sylius\Component\Locale\Model\Locale;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Webgriffe\SyliusActiveCampaignPlugin\Stub\ActiveCampaignContactClientStub;
use Webgriffe\SyliusActiveCampaignPlugin\Message\EcommerceCustomer\EcommerceCustomerCreate;
use Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\EcommerceCustomer\EcommerceCustomerCreateHandler;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface;

final class EcommerceCustomerCreateHandlerTest extends KernelTestCase
{
    private CustomerRepositoryInterface $customerRepository;

    private ChannelRepositoryInterface $channelRepository;

    private EcommerceCustomerCreateHandler $ecommerceCustomerCreateHandler;

    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entityManager = self::getContainer()->get('doctrine.orm.entity_manager');
        $purger = new ORMPurger($this->entityManager);
        $purger->purge();

        $this->customerRepository = self::getContainer()->get('sylius.repository.customer');
        $this->channelRepository = self::getContainer()->get('sylius.repository.channel');

        $this->ecommerceCustomerCreateHandler = new EcommerceCustomerCreateHandler(
            self::getContainer()->get('webgriffe.sylius_active_campaign_plugin.mapper.ecommerce_customer'),
            new ActiveCampaignContactClientStub(),
            $this->customerRepository,
            $this->channelRepository
        );
    }

    public function test_that_it_creates_ecommerce_customer_on_active_campaign(): void
    {
        $this->markTestIncomplete('todo');
        $channel = $this->createChannel();
        $customer = $this->createCustomer();
        $this->ecommerceCustomerCreateHandler->__invoke(new EcommerceCustomerCreate($customer->getId(), $channel->getId()));

        /** @var CustomerInterface&ActiveCampaignAwareInterface $customer */
        $customer = $this->customerRepository->find($customer->getId());
        $this->assertEquals(1234, $customer->getActiveCampaignId());
    }

    // todo: we should start using fixtures
    private function createCustomer(): CustomerInterface
    {
        $customer = new Customer();
        $customer->setEmail('info@activecampaign.com');
        $this->customerRepository->add($customer);

        return $customer;
    }

    private function createChannel(): ChannelInterface
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
        $this->channelRepository->add($channel);

        return $channel;
    }
}
