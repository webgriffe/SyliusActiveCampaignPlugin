<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusActiveCampaignPlugin\Integration\Command;

use Fidry\AliceDataFixtures\Persistence\PurgeMode;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Symfony\Component\Messenger\Envelope;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Contact\ContactCreate;
use Webgriffe\SyliusActiveCampaignPlugin\Message\EcommerceCustomer\EcommerceCustomerCreate;

final class EnqueueContactAndEcommerceCustomerCommandTest extends AbstractCommandTest
{
    private const FIXTURE_BASE_DIR = __DIR__ . '/../../DataFixtures/ORM/resources/Command/EnqueueContactAndEcommerceCustomerCommandTest';

    private CustomerRepositoryInterface $customerRepository;

    private ChannelRepositoryInterface $channelRepository;

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
    }

    public function test_that_it_enqueues_contact_and_ecommerce_customer(): void
    {
        $customer = $this->customerRepository->findOneBy(['email' => 'jim@email.com']);
        $fashionShopChannel = $this->channelRepository->findOneBy(['code' => 'fashion_shop']);
        $digitalShopChannel = $this->channelRepository->findOneBy(['code' => 'digital_shop']);
        self::assertNotNull($customer->getId());
        $commandTester = $this->executeCommand([
            'customer-id' => $customer->getId(),
        ], []);
        self::assertEquals(0, $commandTester->getStatusCode());

        $transport = self::getContainer()->get('messenger.transport.main');
        /** @var Envelope[] $messages */
        $messages = $transport->get();
        $this->assertCount(3, $messages);
        $message = $messages[0];
        $this->assertInstanceOf(ContactCreate::class, $message->getMessage());
        $this->assertEquals($customer->getId(), $message->getMessage()->getCustomerId());
        $message = $messages[1];
        $this->assertInstanceOf(EcommerceCustomerCreate::class, $message->getMessage());
        $this->assertEquals($customer->getId(), $message->getMessage()->getCustomerId());
        $this->assertEquals($fashionShopChannel->getId(), $message->getMessage()->getChannelId());
        $message = $messages[2];
        $this->assertInstanceOf(EcommerceCustomerCreate::class, $message->getMessage());
        $this->assertEquals($customer->getId(), $message->getMessage()->getCustomerId());
        $this->assertEquals($digitalShopChannel->getId(), $message->getMessage()->getChannelId());
    }

    public function test_that_it_enqueues_contact_and_ecommerce_customer_interactively(): void
    {
        $customer = $this->customerRepository->findOneBy(['email' => 'jim@email.com']);
        $fashionShopChannel = $this->channelRepository->findOneBy(['code' => 'fashion_shop']);
        $digitalShopChannel = $this->channelRepository->findOneBy(['code' => 'digital_shop']);
        self::assertNotNull($customer->getId());
        $commandTester = $this->executeCommand([], [
            $customer->getId(),
        ]);
        self::assertEquals(0, $commandTester->getStatusCode());

        $transport = self::getContainer()->get('messenger.transport.main');
        /** @var Envelope[] $messages */
        $messages = $transport->get();
        $this->assertCount(3, $messages);
        $message = $messages[0];
        $this->assertInstanceOf(ContactCreate::class, $message->getMessage());
        $this->assertEquals($customer->getId(), $message->getMessage()->getCustomerId());
        $message = $messages[1];
        $this->assertInstanceOf(EcommerceCustomerCreate::class, $message->getMessage());
        $this->assertEquals($customer->getId(), $message->getMessage()->getCustomerId());
        $this->assertEquals($fashionShopChannel->getId(), $message->getMessage()->getChannelId());
        $message = $messages[2];
        $this->assertInstanceOf(EcommerceCustomerCreate::class, $message->getMessage());
        $this->assertEquals($customer->getId(), $message->getMessage()->getCustomerId());
        $this->assertEquals($digitalShopChannel->getId(), $message->getMessage()->getChannelId());
    }

    public function test_that_it_enqueues_all_contacts_and_ecommerce_customers(): void
    {
        $commandTester = $this->executeCommand([
            '--all' => true,
        ]);
        self::assertEquals(0, $commandTester->getStatusCode());

        $customerJim = $this->customerRepository->findOneBy(['email' => 'jim@email.com']);
        $customerBob = $this->customerRepository->findOneBy(['email' => 'bob@email.com']);
        $fashionShopChannel = $this->channelRepository->findOneBy(['code' => 'fashion_shop']);
        $digitalShopChannel = $this->channelRepository->findOneBy(['code' => 'digital_shop']);
        $transport = self::getContainer()->get('messenger.transport.main');
        /** @var Envelope[] $messages */
        $messages = $transport->get();
        $this->assertCount(6, $messages);
        $message = $messages[0];
        $this->assertInstanceOf(ContactCreate::class, $message->getMessage());
        $this->assertEquals($customerJim->getId(), $message->getMessage()->getCustomerId());
        $message = $messages[1];
        $this->assertInstanceOf(EcommerceCustomerCreate::class, $message->getMessage());
        $this->assertEquals($customerJim->getId(), $message->getMessage()->getCustomerId());
        $this->assertEquals($fashionShopChannel->getId(), $message->getMessage()->getChannelId());
        $message = $messages[2];
        $this->assertInstanceOf(EcommerceCustomerCreate::class, $message->getMessage());
        $this->assertEquals($customerJim->getId(), $message->getMessage()->getCustomerId());
        $this->assertEquals($digitalShopChannel->getId(), $message->getMessage()->getChannelId());
        $message = $messages[3];
        $this->assertInstanceOf(ContactCreate::class, $message->getMessage());
        $this->assertEquals($customerBob->getId(), $message->getMessage()->getCustomerId());
        $message = $messages[4];
        $this->assertInstanceOf(EcommerceCustomerCreate::class, $message->getMessage());
        $this->assertEquals($customerBob->getId(), $message->getMessage()->getCustomerId());
        $this->assertEquals($fashionShopChannel->getId(), $message->getMessage()->getChannelId());
        $message = $messages[5];
        $this->assertInstanceOf(EcommerceCustomerCreate::class, $message->getMessage());
        $this->assertEquals($customerBob->getId(), $message->getMessage()->getCustomerId());
        $this->assertEquals($digitalShopChannel->getId(), $message->getMessage()->getChannelId());
    }

    protected function getCommandDefinition(): string
    {
        return 'webgriffe.sylius_active_campaign_plugin.command.enqueue_contact_and_ecommerce_customer';
    }
}
