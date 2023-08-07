<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusActiveCampaignPlugin\Integration\EventSubscriber;

use Fidry\AliceDataFixtures\Persistence\PurgeMode;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\InMemoryTransport;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Contact\ContactCreate;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Contact\ContactListsSubscriber;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Contact\ContactRemove;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Contact\ContactTagsAdder;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Contact\ContactUpdate;
use Webgriffe\SyliusActiveCampaignPlugin\Message\EcommerceCustomer\EcommerceCustomerCreate;
use Webgriffe\SyliusActiveCampaignPlugin\Message\EcommerceCustomer\EcommerceCustomerRemove;
use Webgriffe\SyliusActiveCampaignPlugin\Message\EcommerceCustomer\EcommerceCustomerUpdate;
use Webgriffe\SyliusActiveCampaignPlugin\Model\CustomerActiveCampaignAwareInterface;
use Webmozart\Assert\Assert;

final class CustomerSubscriberTest extends AbstractEventDispatcherTest
{
    private const FIXTURE_BASE_DIR = __DIR__ . '/../../DataFixtures/ORM/resources/EventSubscriber/CustomerSubscriberTest';

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

    public function test_that_it_creates_new_contact_and_new_ecommerce_customer_on_active_campaign(): void
    {
        $customerJim = $this->customerRepository->findOneBy(['email' => 'jim@email.com']);
        $fashionChannel = $this->channelRepository->findOneBy(['code' => 'fashion_shop']);
        $digitalChannel = $this->channelRepository->findOneBy(['code' => 'digital_shop']);
        $this->eventDispatcher->dispatch(new ResourceControllerEvent($customerJim), 'sylius.customer.post_create');
        /** @var InMemoryTransport $transport */
        $transport = self::getContainer()->get('messenger.transport.main');
        /** @var Envelope[] $messages */
        $messages = $transport->get();
        $this->assertCount(5, $messages);
        $message = self::getMessageFromCustomerTypeAndChannel($messages, $customerJim, ContactCreate::class);
        $this->assertInstanceOf(ContactCreate::class, $message->getMessage());
        $this->assertEquals($customerJim->getId(), $message->getMessage()->getCustomerId());
        $message = self::getMessageFromCustomerTypeAndChannel($messages, $customerJim, EcommerceCustomerCreate::class, $fashionChannel);
        $this->assertInstanceOf(EcommerceCustomerCreate::class, $message->getMessage());
        $this->assertEquals($customerJim->getId(), $message->getMessage()->getCustomerId());
        $this->assertEquals($fashionChannel->getId(), $message->getMessage()->getChannelId());
        $message = self::getMessageFromCustomerTypeAndChannel($messages, $customerJim, EcommerceCustomerCreate::class, $digitalChannel);
        $this->assertInstanceOf(EcommerceCustomerCreate::class, $message->getMessage());
        $this->assertEquals($customerJim->getId(), $message->getMessage()->getCustomerId());
        $this->assertEquals($digitalChannel->getId(), $message->getMessage()->getChannelId());
        $message = self::getMessageFromCustomerTypeAndChannel($messages, $customerJim, ContactTagsAdder::class);
        $this->assertInstanceOf(ContactTagsAdder::class, $message->getMessage());
        $this->assertEquals($customerJim->getId(), $message->getMessage()->getCustomerId());
        $message = self::getMessageFromCustomerTypeAndChannel($messages, $customerJim, ContactListsSubscriber::class);
        $this->assertInstanceOf(ContactListsSubscriber::class, $message->getMessage());
        $this->assertEquals($customerJim->getId(), $message->getMessage()->getCustomerId());
    }

    public function test_that_it_updates_existing_contact_and_existing_ecommerce_customer_on_active_campaign(): void
    {
        /** @var CustomerActiveCampaignAwareInterface $customerBob */
        $customerBob = $this->customerRepository->findOneBy(['email' => 'bob@email.com']);
        $fashionChannel = $this->channelRepository->findOneBy(['code' => 'fashion_shop']);
        $digitalChannel = $this->channelRepository->findOneBy(['code' => 'digital_shop']);
        $this->eventDispatcher->dispatch(new ResourceControllerEvent($customerBob), 'sylius.customer.post_update');
        /** @var InMemoryTransport $transport */
        $transport = self::getContainer()->get('messenger.transport.main');
        /** @var Envelope[] $messages */
        $messages = $transport->get();
        $this->assertCount(5, $messages);
        $message = self::getMessageFromCustomerTypeAndChannel($messages, $customerBob, ContactUpdate::class);
        $this->assertInstanceOf(ContactUpdate::class, $message->getMessage());
        $this->assertEquals($customerBob->getId(), $message->getMessage()->getCustomerId());
        $this->assertEquals($customerBob->getActiveCampaignId(), $message->getMessage()->getActiveCampaignId());
        $message = self::getMessageFromCustomerTypeAndChannel($messages, $customerBob, EcommerceCustomerUpdate::class, $fashionChannel);
        $this->assertInstanceOf(EcommerceCustomerUpdate::class, $message->getMessage());
        $this->assertEquals($customerBob->getId(), $message->getMessage()->getCustomerId());
        $this->assertEquals(576, $message->getMessage()->getActiveCampaignId());
        $this->assertEquals($fashionChannel->getId(), $message->getMessage()->getChannelId());
        $message = self::getMessageFromCustomerTypeAndChannel($messages, $customerBob, EcommerceCustomerCreate::class, $digitalChannel);
        $this->assertInstanceOf(EcommerceCustomerCreate::class, $message->getMessage());
        $this->assertEquals($customerBob->getId(), $message->getMessage()->getCustomerId());
        $this->assertEquals($digitalChannel->getId(), $message->getMessage()->getChannelId());
        $message = self::getMessageFromCustomerTypeAndChannel($messages, $customerBob, ContactTagsAdder::class);
        $this->assertInstanceOf(ContactTagsAdder::class, $message->getMessage());
        $this->assertEquals($customerBob->getId(), $message->getMessage()->getCustomerId());
        $message = self::getMessageFromCustomerTypeAndChannel($messages, $customerBob, ContactListsSubscriber::class);
        $this->assertInstanceOf(ContactListsSubscriber::class, $message->getMessage());
        $this->assertEquals($customerBob->getId(), $message->getMessage()->getCustomerId());
    }

    public function test_that_it_removes_contact_and_ecommerce_customer_on_active_campaign(): void
    {
        $customer = $this->customerRepository->findOneBy(['email' => 'sam@email.com']);
        $this->eventDispatcher->dispatch(new ResourceControllerEvent($customer), 'sylius.customer.post_delete');
        /** @var InMemoryTransport $transport */
        $transport = self::getContainer()->get('messenger.transport.main');
        /** @var Envelope[] $messages */
        $messages = $transport->get();
        $this->assertCount(3, $messages);
        $message = $messages[0];
        $this->assertInstanceOf(ContactRemove::class, $message->getMessage());
        $this->assertEquals($customer->getActiveCampaignId(), $message->getMessage()->getActiveCampaignId());
        $message = $messages[1];
        $this->assertInstanceOf(EcommerceCustomerRemove::class, $message->getMessage());
        $this->assertEquals(13, $message->getMessage()->getActiveCampaignId());
        $message = $messages[2];
        $this->assertInstanceOf(EcommerceCustomerRemove::class, $message->getMessage());
        $this->assertEquals(143, $message->getMessage()->getActiveCampaignId());
    }

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
