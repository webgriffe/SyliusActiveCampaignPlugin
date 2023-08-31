<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusActiveCampaignPlugin\Integration\Controller;

use Fidry\AliceDataFixtures\Persistence\PurgeMode;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\InMemoryTransport;
use Webgriffe\SyliusActiveCampaignPlugin\Controller\WebhookController;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Contact\ContactAutomationEvent;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Contact\ContactListsUpdater;

final class WebhookControllerTest extends KernelTestCase
{
    private const FIXTURE_BASE_DIR = __DIR__ . '/../../DataFixtures/ORM/resources/Controller/WebhookControllerTest';

    private WebhookController $webhookController;

    private CustomerRepositoryInterface $customerRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->webhookController = self::getContainer()->get('webgriffe.sylius_active_campaign_plugin.controller.webhook');
        $this->customerRepository = self::getContainer()->get('sylius.repository.customer');
        $fixtureLoader = self::getContainer()->get('fidry_alice_data_fixtures.loader.doctrine');
        $fixtureLoader->load([
            self::FIXTURE_BASE_DIR . '/customers.yaml',
        ], [], [], PurgeMode::createDeleteMode());
    }

    public function test_that_it_updates_contact_lists_status(): void
    {
        $bobCustomer = $this->customerRepository->findOneBy(['email' => 'bob@email.com']);

        $this->webhookController->updateListStatusAction(new Request([], [
            'type' => 'subscribe',
            'date_time' => '2013-07-18 08:46:33',
            'initiated_from' => 'admin',
            'initiated_by' => 'admin',
            'list' => '1',
            'contact' => [
                'id' => '50984',
                'email' => 'bob@email.com',
                'first_name' => 'test',
                'last_name' => 'test',
                'ip' => '127.0.0.1',
            ],
        ]));

        /** @var InMemoryTransport $transport */
        $transport = self::getContainer()->get('messenger.transport.main');
        /** @var Envelope[] $messages */
        $messages = $transport->get();
        $this->assertCount(1, $messages);
        $message = $messages[0];
        $this->assertInstanceOf(ContactListsUpdater::class, $message->getMessage());
        $this->assertEquals($bobCustomer->getId(), $message->getMessage()->getCustomerId());
    }

    public function test_that_it_does_not_updates_contact_lists_status_if_the_customer_for_that_contact_does_not_exists(): void
    {
        $this->webhookController->updateListStatusAction(new Request([], [
            'type' => 'subscribe',
            'date_time' => '2013-07-18 08:46:33',
            'initiated_from' => 'admin',
            'initiated_by' => 'admin',
            'list' => '1',
            'contact' => [
                'id' => '50983',
                'email' => 'bob@email.com',
                'first_name' => 'test',
                'last_name' => 'test',
                'ip' => '127.0.0.1',
            ],
        ]));

        /** @var InMemoryTransport $transport */
        $transport = self::getContainer()->get('messenger.transport.main');
        /** @var Envelope[] $messages */
        $messages = $transport->get();
        $this->assertCount(0, $messages);
    }

    public function test_that_it_dispatch_contact_automation_event(): void
    {
        $bobCustomer = $this->customerRepository->findOneBy(['email' => 'bob@email.com']);

        $this->webhookController->dispatchContactAutomationEventAction(new Request([
            'promotionCode' => 'PROMOTION_CODE',
            'other_query_param' => 23,
        ], [
            'seriesid' => '1',
            'contact' => [
                'id' => '50984',
                'email' => 'bob@email.com',
                'first_name' => 'test',
                'last_name' => 'test',
                'phone' => '',
                'tags' => 'webhook,othertag',
                'customer_acct_name' => '',
                'orgname' => 'Webgriffe',
                'ip4' => '127.0.0.1',
            ],
        ]));

        /** @var InMemoryTransport $transport */
        $transport = self::getContainer()->get('messenger.transport.main');
        /** @var Envelope[] $messages */
        $messages = $transport->get();
        $this->assertCount(1, $messages);
        $message = $messages[0];
        $this->assertInstanceOf(ContactAutomationEvent::class, $message->getMessage());
        $this->assertEquals($bobCustomer->getId(), $message->getMessage()->getCustomerId());
        $this->assertEquals('1', $message->getMessage()->getAutomationId());
        $this->assertEquals([
            'promotionCode' => 'PROMOTION_CODE',
            'other_query_param' => 23,
        ], $message->getMessage()->getPayload());
    }
}
