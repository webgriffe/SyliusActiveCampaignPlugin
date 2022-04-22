<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusActiveCampaignPlugin\Integration\Command;

use Fidry\AliceDataFixtures\Persistence\PurgeMode;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Symfony\Component\Messenger\Envelope;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Contact\ContactListsUpdater;

final class UpdateContactListsSubscriptionCommandTest extends AbstractCommandTest
{
    private const FIXTURE_BASE_DIR = __DIR__ . '/../../DataFixtures/ORM/resources/Command/UpdateContactListsSubscriptionCommandTest';

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

    public function test_that_it_enqueues_contact_lists_subscription(): void
    {
        $customer = $this->customerRepository->findOneBy(['email' => 'jim@email.com']);
        self::assertNotNull($customer->getId());

        $commandTester = $this->executeCommand([
            'customer-id' => $customer->getId(),
        ], []);

        self::assertEquals(0, $commandTester->getStatusCode());

        $transport = self::getContainer()->get('messenger.transport.main');
        /** @var Envelope[] $messages */
        $messages = $transport->get();
        $this->assertCount(1, $messages);
        $message = $messages[0];
        $this->assertInstanceOf(ContactListsUpdater::class, $message->getMessage());
        $this->assertEquals($customer->getId(), $message->getMessage()->getCustomerId());
    }

    public function test_that_it_enqueues_contact_lists_subscription_interactively(): void
    {
        $customer = $this->customerRepository->findOneBy(['email' => 'jim@email.com']);
        self::assertNotNull($customer->getId());

        $commandTester = $this->executeCommand([], [
            $customer->getId(),
        ]);

        self::assertEquals(0, $commandTester->getStatusCode());
        $transport = self::getContainer()->get('messenger.transport.main');
        /** @var Envelope[] $messages */
        $messages = $transport->get();
        $this->assertCount(1, $messages);
        $message = $messages[0];
        $this->assertInstanceOf(ContactListsUpdater::class, $message->getMessage());
        $this->assertEquals($customer->getId(), $message->getMessage()->getCustomerId());
    }

    public function test_that_it_enqueues_all_contacts_lists_subscription(): void
    {
        $commandTester = $this->executeCommand([
            '--all' => true,
        ]);

        self::assertEquals(0, $commandTester->getStatusCode());

        $customerJim = $this->customerRepository->findOneBy(['email' => 'jim@email.com']);
        $customerBob = $this->customerRepository->findOneBy(['email' => 'bob@email.com']);
        $customerSam = $this->customerRepository->findOneBy(['email' => 'sam@email.com']);
        $transport = self::getContainer()->get('messenger.transport.main');
        /** @var Envelope[] $messages */
        $messages = $transport->get();
        $this->assertCount(3, $messages);

        $message = $messages[0];
        $this->assertInstanceOf(ContactListsUpdater::class, $message->getMessage());
        $this->assertEquals($customerJim->getId(), $message->getMessage()->getCustomerId());
        $message = $messages[1];
        $this->assertInstanceOf(ContactListsUpdater::class, $message->getMessage());
        $this->assertEquals($customerBob->getId(), $message->getMessage()->getCustomerId());
        $message = $messages[2];
        $this->assertInstanceOf(ContactListsUpdater::class, $message->getMessage());
        $this->assertEquals($customerSam->getId(), $message->getMessage()->getCustomerId());
    }

    protected function getCommandDefinition(): string
    {
        return 'webgriffe.sylius_active_campaign_plugin.command.update_contact_lists_subscription';
    }
}
