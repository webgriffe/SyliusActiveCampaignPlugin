<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusActiveCampaignPlugin\Integration\Command;

use Fidry\AliceDataFixtures\Persistence\PurgeMode;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Symfony\Component\Messenger\Envelope;
use Tests\Webgriffe\SyliusActiveCampaignPlugin\Stub\ActiveCampaignContactClientStub;
use Tests\Webgriffe\SyliusActiveCampaignPlugin\Stub\ActiveCampaignEcommerceCustomerClientStub;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Contact\ContactCreate;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Contact\ContactUpdate;
use Webgriffe\SyliusActiveCampaignPlugin\Message\EcommerceCustomer\EcommerceCustomerCreate;
use Webgriffe\SyliusActiveCampaignPlugin\Message\EcommerceCustomer\EcommerceCustomerUpdate;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Contact\ContactResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\EcommerceCustomer\EcommerceCustomerResponse;
use Webmozart\Assert\Assert;

final class EnqueueContactAndEcommerceCustomerCommandTest extends AbstractCommandTest
{
    private const FIXTURE_BASE_DIR = __DIR__ . '/../../DataFixtures/ORM/resources/Command/EnqueueContactAndEcommerceCustomerCommandTest';

    private CustomerRepositoryInterface $customerRepository;

    private ChannelRepositoryInterface $channelRepository;

    protected function setUp(): void
    {
        parent::setUp();
        ActiveCampaignContactClientStub::$activeCampaignResources = [
            'sam@email.com' => (new ContactResponse(143)),
        ];
        ActiveCampaignEcommerceCustomerClientStub::$activeCampaignResources = [
            [
                'email' => 'sam@email.com',
                'connectionid' => '5',
                'ecommerceCustomer' => new EcommerceCustomerResponse(765),
            ],
            [
                'email' => 'sam@email.com',
                'connectionid' => '18',
                'ecommerceCustomer' => new EcommerceCustomerResponse(989),
            ],
        ];
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
        $message = self::getMessageFromCustomerTypeAndChannel($messages, $customer, ContactCreate::class);
        $this->assertInstanceOf(ContactCreate::class, $message->getMessage());
        $this->assertEquals($customer->getId(), $message->getMessage()->getCustomerId());
        $message = self::getMessageFromCustomerTypeAndChannel($messages, $customer, EcommerceCustomerCreate::class, $fashionShopChannel);
        $this->assertInstanceOf(EcommerceCustomerCreate::class, $message->getMessage());
        $this->assertEquals($customer->getId(), $message->getMessage()->getCustomerId());
        $this->assertEquals($fashionShopChannel->getId(), $message->getMessage()->getChannelId());
        $message = self::getMessageFromCustomerTypeAndChannel($messages, $customer, EcommerceCustomerCreate::class, $digitalShopChannel);
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
        $message = self::getMessageFromCustomerTypeAndChannel($messages, $customer, ContactCreate::class);
        $this->assertInstanceOf(ContactCreate::class, $message->getMessage());
        $this->assertEquals($customer->getId(), $message->getMessage()->getCustomerId());
        $message = self::getMessageFromCustomerTypeAndChannel($messages, $customer, EcommerceCustomerCreate::class, $fashionShopChannel);
        $this->assertInstanceOf(EcommerceCustomerCreate::class, $message->getMessage());
        $this->assertEquals($customer->getId(), $message->getMessage()->getCustomerId());
        $this->assertEquals($fashionShopChannel->getId(), $message->getMessage()->getChannelId());
        $message = self::getMessageFromCustomerTypeAndChannel($messages, $customer, EcommerceCustomerCreate::class, $digitalShopChannel);
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
        $customerSam = $this->customerRepository->findOneBy(['email' => 'sam@email.com']);
        $fashionShopChannel = $this->channelRepository->findOneBy(['code' => 'fashion_shop']);
        $digitalShopChannel = $this->channelRepository->findOneBy(['code' => 'digital_shop']);
        $transport = self::getContainer()->get('messenger.transport.main');
        /** @var Envelope[] $messages */
        $messages = $transport->get();
        $this->assertCount(9, $messages);

        $message = self::getMessageFromCustomerTypeAndChannel($messages, $customerJim, ContactCreate::class);
        $this->assertInstanceOf(ContactCreate::class, $message->getMessage());
        $this->assertEquals($customerJim->getId(), $message->getMessage()->getCustomerId());
        $message = self::getMessageFromCustomerTypeAndChannel($messages, $customerJim, EcommerceCustomerCreate::class, $fashionShopChannel);
        $this->assertInstanceOf(EcommerceCustomerCreate::class, $message->getMessage());
        $this->assertEquals($customerJim->getId(), $message->getMessage()->getCustomerId());
        $this->assertEquals($fashionShopChannel->getId(), $message->getMessage()->getChannelId());
        $message = self::getMessageFromCustomerTypeAndChannel($messages, $customerJim, EcommerceCustomerCreate::class, $digitalShopChannel);
        $this->assertInstanceOf(EcommerceCustomerCreate::class, $message->getMessage());
        $this->assertEquals($customerJim->getId(), $message->getMessage()->getCustomerId());
        $this->assertEquals($digitalShopChannel->getId(), $message->getMessage()->getChannelId());

        $message = self::getMessageFromCustomerTypeAndChannel($messages, $customerBob, ContactUpdate::class);
        $this->assertInstanceOf(ContactUpdate::class, $message->getMessage());
        $this->assertEquals($customerBob->getId(), $message->getMessage()->getCustomerId());
        $this->assertEquals(32, $message->getMessage()->getActiveCampaignId());
        $message = self::getMessageFromCustomerTypeAndChannel($messages, $customerBob, EcommerceCustomerUpdate::class, $fashionShopChannel);
        $this->assertInstanceOf(EcommerceCustomerUpdate::class, $message->getMessage());
        $this->assertEquals($customerBob->getId(), $message->getMessage()->getCustomerId());
        $this->assertEquals($fashionShopChannel->getId(), $message->getMessage()->getChannelId());
        $this->assertEquals(576, $message->getMessage()->getActiveCampaignId());
        $message = self::getMessageFromCustomerTypeAndChannel($messages, $customerBob, EcommerceCustomerUpdate::class, $digitalShopChannel);
        $this->assertInstanceOf(EcommerceCustomerUpdate::class, $message->getMessage());
        $this->assertEquals($customerBob->getId(), $message->getMessage()->getCustomerId());
        $this->assertEquals($digitalShopChannel->getId(), $message->getMessage()->getChannelId());
        $this->assertEquals(234, $message->getMessage()->getActiveCampaignId());

        $message = self::getMessageFromCustomerTypeAndChannel($messages, $customerSam, ContactUpdate::class);
        $this->assertInstanceOf(ContactUpdate::class, $message->getMessage());
        $this->assertEquals($customerSam->getId(), $message->getMessage()->getCustomerId());
        $this->assertEquals(143, $message->getMessage()->getActiveCampaignId());
        $message = self::getMessageFromCustomerTypeAndChannel($messages, $customerSam, EcommerceCustomerUpdate::class, $fashionShopChannel);
        $this->assertInstanceOf(EcommerceCustomerUpdate::class, $message->getMessage());
        $this->assertEquals($customerSam->getId(), $message->getMessage()->getCustomerId());
        $this->assertEquals($fashionShopChannel->getId(), $message->getMessage()->getChannelId());
        $this->assertEquals(765, $message->getMessage()->getActiveCampaignId());
        $message = self::getMessageFromCustomerTypeAndChannel($messages, $customerSam, EcommerceCustomerUpdate::class, $digitalShopChannel);
        $this->assertInstanceOf(EcommerceCustomerUpdate::class, $message->getMessage());
        $this->assertEquals($customerSam->getId(), $message->getMessage()->getCustomerId());
        $this->assertEquals($digitalShopChannel->getId(), $message->getMessage()->getChannelId());
        $this->assertEquals(989, $message->getMessage()->getActiveCampaignId());
    }

    protected function getCommandDefinition(): string
    {
        return 'webgriffe.sylius_active_campaign_plugin.command.enqueue_contact_and_ecommerce_customer';
    }

    /**
     * @param Envelope[] $messages
     * @param class-string $messageClass
     */
    private static function getMessageFromCustomerTypeAndChannel(
        array $messages,
        CustomerInterface $customer,
        string $messageClass,
        ChannelInterface $channel = null
    ): Envelope {
        $messages = array_filter($messages, static function (Envelope $envelope) use ($customer, $messageClass, $channel) {
            $message = $envelope->getMessage();
            if (!$message instanceof $messageClass) {
                return false;
            }
            if ($channel !== null && $message->getChannelId() !== $channel->getId()) {
                return false;
            }

            return $message->getCustomerId() === $customer->getId();
        });
        Assert::count($messages, 1);

        return reset($messages);
    }
}
