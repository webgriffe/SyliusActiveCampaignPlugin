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
use Tests\Webgriffe\SyliusActiveCampaignPlugin\Stub\ActiveCampaignEcommerceCustomerClientStub;
use Webgriffe\SyliusActiveCampaignPlugin\Message\EcommerceCustomer\EcommerceCustomerUpdate;
use Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\EcommerceCustomer\EcommerceCustomerUpdateHandler;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface;

final class EcommerceCustomerUpdateHandlerTest extends KernelTestCase
{
    private CustomerRepositoryInterface $customerRepository;

    private ChannelRepositoryInterface $channelRepository;

    private EcommerceCustomerUpdateHandler $ecommerceCustomerUpdateHandler;

    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entityManager = self::getContainer()->get('doctrine.orm.entity_manager');
        $purger = new ORMPurger($this->entityManager);
        $purger->purge();

        $this->customerRepository = self::getContainer()->get('sylius.repository.customer');
        $this->channelRepository = self::getContainer()->get('sylius.repository.channel');

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
        $channel = $this->createChannel();
        $customer = $this->createCustomer();
        $this->ecommerceCustomerUpdateHandler->__invoke(new EcommerceCustomerUpdate($customer->getId(), $customer->getActiveCampaignId(), $channel->getId()));

        /** @var CustomerInterface&ActiveCampaignAwareInterface $customer */
        $customer = $this->customerRepository->find($customer->getId());
        $this->assertEquals(1234, $customer->getActiveCampaignId());
    }

    // todo: we should start using fixtures
    /**
     * @return CustomerInterface&ActiveCampaignAwareInterface
     */
    private function createCustomer()
    {
        $customer = new Customer();
        $customer->setEmail('info@activecampaign.com');
        $customer->setSubscribedToNewsletter(true);
        $customer->setActiveCampaignId(1234);
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
