<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusActiveCampaignPlugin\Integration\Command;

use App\Entity\Customer\Customer;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Symfony\Component\Messenger\Envelope;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Contact\ContactCreate;

final class EnqueueContactCommandTest extends AbstractCommandTest
{
    private CustomerRepositoryInterface $customerRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $entityManager = self::getContainer()->get('doctrine.orm.entity_manager');
        $purger = new ORMPurger($entityManager);
        $purger->purge();
        $this->customerRepository = self::getContainer()->get('sylius.repository.customer');
    }

    public function test_that_it_enqueues_contact(): void
    {
        $customer = $this->createCustomer();
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
        $this->assertInstanceOf(ContactCreate::class, $message->getMessage());
        $this->assertEquals($customer->getId(), $message->getMessage()->getCustomerId());
    }

    public function test_that_it_enqueues_contact_interactively(): void
    {
        $customer = $this->createCustomer();
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
        $this->assertInstanceOf(ContactCreate::class, $message->getMessage());
        $this->assertEquals($customer->getId(), $message->getMessage()->getCustomerId());
    }

    public function test_that_it_enqueues_all_contacts(): void
    {
        $firstCustomer = $this->createCustomer('info@activecampaign.com');
        $secondCustomer = $this->createCustomer('test@activecampaign.com');
        $commandTester = $this->executeCommand([
            '--all' => true,
        ]);
        self::assertEquals(0, $commandTester->getStatusCode());

        $transport = self::getContainer()->get('messenger.transport.main');
        /** @var Envelope[] $messages */
        $messages = $transport->get();
        $this->assertCount(2, $messages);
        $message = $messages[0];
        $this->assertInstanceOf(ContactCreate::class, $message->getMessage());
        $this->assertEquals($firstCustomer->getId(), $message->getMessage()->getCustomerId());
        $message = $messages[1];
        $this->assertInstanceOf(ContactCreate::class, $message->getMessage());
        $this->assertEquals($secondCustomer->getId(), $message->getMessage()->getCustomerId());
    }

    protected function getCommandDefinition(): string
    {
        return 'webgriffe.sylius_active_campaign_plugin.command.enqueue_contact';
    }

    private function createCustomer(string $email = 'info@activecampaign.com'): Customer
    {
        $customer = new Customer();
        $customer->setEmail($email);
        $this->customerRepository->add($customer);

        return $customer;
    }
}
