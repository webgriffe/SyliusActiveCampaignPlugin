<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusActiveCampaignPlugin\Integration\MessageHandler;

use App\Entity\Customer\Customer;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Webgriffe\SyliusActiveCampaignPlugin\Stub\ActiveCampaignContactClientStub;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Contact\ContactCreate;
use Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\Contact\ContactCreateHandler;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface;

final class ContactCreateHandlerTest extends KernelTestCase
{
    private CustomerRepositoryInterface $customerRepository;

    private ContactCreateHandler $contactCreateHandler;

    protected function setUp(): void
    {
        parent::setUp();
        $entityManager = self::getContainer()->get('doctrine.orm.entity_manager');
        $purger = new ORMPurger($entityManager);
        $purger->purge();
        $this->customerRepository = self::getContainer()->get('sylius.repository.customer');
        $this->contactCreateHandler = new ContactCreateHandler(
            self::getContainer()->get('webgriffe.sylius_active_campaign_plugin.mapper.contact'),
            new ActiveCampaignContactClientStub(),
            $this->customerRepository
        );
    }

    public function test_that_it_creates_contact_on_active_campaign(): void
    {
        $customer = $this->createCustomer();
        $this->contactCreateHandler->__invoke(new ContactCreate($customer->getId()));

        /** @var CustomerInterface&ActiveCampaignAwareInterface $customer */
        $customer = $this->customerRepository->find($customer->getId());
        $this->assertEquals(1234, $customer->getActiveCampaignId());
    }

    private function createCustomer(): CustomerInterface
    {
        $customer = new Customer();
        $customer->setEmail('info@activecampaign.com');
        $this->customerRepository->add($customer);

        return $customer;
    }
}
