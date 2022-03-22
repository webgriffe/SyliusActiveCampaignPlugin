<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusActiveCampaignPlugin\Integration\EventSubscriber;

use Fidry\AliceDataFixtures\Persistence\PurgeMode;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\InMemoryTransport;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Contact\ContactCreate;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Contact\ContactRemove;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Contact\ContactUpdate;

final class CustomerSubscriberTest extends AbstractEventDispatcherTest
{
    private const FIXTURE_BASE_DIR = __DIR__ . '/../../DataFixtures/ORM/resources/EventSubscriber/CustomerSubscriberTest';

    private CustomerRepositoryInterface $customerRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->customerRepository = self::getContainer()->get('sylius.repository.customer');

        $fixtureLoader = self::getContainer()->get('fidry_alice_data_fixtures.loader.doctrine');
        $fixtureLoader->load([
            self::FIXTURE_BASE_DIR . '/customers.yaml',
        ], [], [], PurgeMode::createDeleteMode());
    }

    public function test_that_it_creates_contact_on_active_campaign(): void
    {
        $customer = $this->customerRepository->findOneBy(['email' => 'jim@email.com']);
        $this->eventDispatcher->dispatch(new ResourceControllerEvent($customer), 'sylius.customer.post_create');
        /** @var InMemoryTransport $transport */
        $transport = self::getContainer()->get('messenger.transport.main');
        /** @var Envelope[] $messages */
        $messages = $transport->get();
        $this->assertCount(1, $messages);
        $message = $messages[0];
        $this->assertInstanceOf(ContactCreate::class, $message->getMessage());
        $this->assertEquals($customer->getId(), $message->getMessage()->getCustomerId());
    }

    public function test_that_it_updates_contact_on_active_campaign(): void
    {
        $customer = $this->customerRepository->findOneBy(['email' => 'jim@email.com']);
        $this->eventDispatcher->dispatch(new ResourceControllerEvent($customer), 'sylius.customer.post_update');
        /** @var InMemoryTransport $transport */
        $transport = self::getContainer()->get('messenger.transport.main');
        /** @var Envelope[] $messages */
        $messages = $transport->get();
        $this->assertCount(1, $messages);
        $message = $messages[0];
        $this->assertInstanceOf(ContactUpdate::class, $message->getMessage());
        $this->assertEquals($customer->getId(), $message->getMessage()->getCustomerId());
        $this->assertEquals($customer->getActiveCampaignId(), $message->getMessage()->getActiveCampaignId());
    }

    public function test_that_it_removes_contact_on_active_campaign(): void
    {
        $customer = $this->customerRepository->findOneBy(['email' => 'jim@email.com']);
        $this->eventDispatcher->dispatch(new ResourceControllerEvent($customer), 'sylius.customer.post_delete');
        /** @var InMemoryTransport $transport */
        $transport = self::getContainer()->get('messenger.transport.main');
        /** @var Envelope[] $messages */
        $messages = $transport->get();
        $this->assertCount(1, $messages);
        $message = $messages[0];
        $this->assertInstanceOf(ContactRemove::class, $message->getMessage());
        $this->assertEquals($customer->getActiveCampaignId(), $message->getMessage()->getActiveCampaignId());
    }
}
