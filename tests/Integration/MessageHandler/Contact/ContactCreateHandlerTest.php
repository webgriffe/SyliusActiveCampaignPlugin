<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusActiveCampaignPlugin\Integration\MessageHandler\Contact;

use Fidry\AliceDataFixtures\Persistence\PurgeMode;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Contact\ContactCreate;
use Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\Contact\ContactCreateHandler;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface;

final class ContactCreateHandlerTest extends KernelTestCase
{
    private const FIXTURE_BASE_DIR = __DIR__ . '/../../../DataFixtures/ORM/resources/MessageHandler/ContactCreateHandlerTest';

    private CustomerRepositoryInterface $customerRepository;

    private ContactCreateHandler $contactCreateHandler;

    protected function setUp(): void
    {
        parent::setUp();
        $this->customerRepository = self::getContainer()->get('sylius.repository.customer');

        $fixtureLoader = self::getContainer()->get('fidry_alice_data_fixtures.loader.doctrine');
        $fixtureLoader->load([
            self::FIXTURE_BASE_DIR . '/customers.yaml',
        ], [], [], PurgeMode::createDeleteMode());

        $this->contactCreateHandler = new ContactCreateHandler(
            self::getContainer()->get('webgriffe.sylius_active_campaign_plugin.mapper.contact'),
            self::getContainer()->get('webgriffe.sylius_active_campaign_plugin.client_stub.active_campaign.contact'),
            $this->customerRepository,
            self::getContainer()->get('messenger.default_bus'),
        );
    }

    public function test_that_it_creates_contact_on_active_campaign(): void
    {
        $customer = $this->customerRepository->findOneBy(['email' => 'jim@email.com']);
        $this->contactCreateHandler->__invoke(new ContactCreate($customer->getId()));

        /** @var CustomerInterface&ActiveCampaignAwareInterface $customer */
        $customer = $this->customerRepository->find($customer->getId());
        $this->assertEquals(1234, $customer->getActiveCampaignId());
    }
}
