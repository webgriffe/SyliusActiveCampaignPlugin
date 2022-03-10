<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusActiveCampaignPlugin\Integration\EventSubscriber;

use App\Entity\Customer\Customer;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\InMemoryTransport;
use Webgriffe\SyliusActiveCampaignPlugin\Message\ContactCreate;

final class CustomerSubscriberTest extends KernelTestCase
{
    protected EventDispatcherInterface $eventDispatcher;

    private CustomerRepositoryInterface $customerRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->eventDispatcher = self::getContainer()->get('event_dispatcher');
        $this->customerRepository = self::getContainer()->get('sylius.repository.customer');
        $entityManager = self::getContainer()->get('doctrine.orm.entity_manager');
        $purger = new ORMPurger($entityManager);
        $purger->purge();
    }

    public function test_that_it_creates_contact_on_active_campaign(): void
    {
        $customer = new Customer();
        $customer->setEmail('info@activecampaign.com');
        $this->customerRepository->add($customer);
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
}
