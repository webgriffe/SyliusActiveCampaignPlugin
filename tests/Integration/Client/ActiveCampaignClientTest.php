<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusActiveCampaignPlugin\Integration\Client;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Webgriffe\SyliusActiveCampaignPlugin\Stub\HttpClientStub;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\Contact;

final class ActiveCampaignClientTest extends KernelTestCase
{
    private ActiveCampaignClientInterface $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = self::getContainer()->get('webgriffe.sylius_active_campaign_plugin.client.active_campaign');
    }

    public function test_it_should_create_contact_on_active_campaign(): void
    {
        HttpClientStub::$responseBodyContent = '{"fieldValues":[],"contact":{"email":"zachary.rippin@wisozk.com","phone":"+1-470-370-2694","firstName":"Bennett","lastName":"Hahn","email_empty":false,"cdate":"2022-03-15T12:01:48-05:00","udate":"2022-03-15T12:01:48-05:00","orgid":"","orgname":"","links":{"bounceLogs":"https:\/\/webgriffe1646663336.api-us1.com\/api\/3\/contacts\/5\/bounceLogs","contactAutomations":"https:\/\/webgriffe1646663336.api-us1.com\/api\/3\/contacts\/5\/contactAutomations?limit=1000&orders%5Blastdate%5D=DESC","contactData":"https:\/\/webgriffe1646663336.api-us1.com\/api\/3\/contacts\/5\/contactData","contactGoals":"https:\/\/webgriffe1646663336.api-us1.com\/api\/3\/contacts\/5\/contactGoals","contactLists":"https:\/\/webgriffe1646663336.api-us1.com\/api\/3\/contacts\/5\/contactLists","contactLogs":"https:\/\/webgriffe1646663336.api-us1.com\/api\/3\/contacts\/5\/contactLogs","contactTags":"https:\/\/webgriffe1646663336.api-us1.com\/api\/3\/contacts\/5\/contactTags","contactDeals":"https:\/\/webgriffe1646663336.api-us1.com\/api\/3\/contacts\/5\/contactDeals","deals":"https:\/\/webgriffe1646663336.api-us1.com\/api\/3\/contacts\/5\/deals","fieldValues":"https:\/\/webgriffe1646663336.api-us1.com\/api\/3\/contacts\/5\/fieldValues","geoIps":"https:\/\/webgriffe1646663336.api-us1.com\/api\/3\/contacts\/5\/geoIps","notes":"https:\/\/webgriffe1646663336.api-us1.com\/api\/3\/contacts\/5\/notes","organization":"https:\/\/webgriffe1646663336.api-us1.com\/api\/3\/contacts\/5\/organization","plusAppend":"https:\/\/webgriffe1646663336.api-us1.com\/api\/3\/contacts\/5\/plusAppend","trackingLogs":"https:\/\/webgriffe1646663336.api-us1.com\/api\/3\/contacts\/5\/trackingLogs","scoreValues":"https:\/\/webgriffe1646663336.api-us1.com\/api\/3\/contacts\/5\/scoreValues","accountContacts":"https:\/\/webgriffe1646663336.api-us1.com\/api\/3\/contacts\/5\/accountContacts","automationEntryCounts":"https:\/\/webgriffe1646663336.api-us1.com\/api\/3\/contacts\/5\/automationEntryCounts"},"hash":"97c021d40fa4b0b31924eb228c1b26bd","fieldValues":[],"id":"5","organization":""}}';
        $contact = new Contact('zachary.rippin@wisozk.com', 'Bennett', 'Hahn', '+1-470-370-2694', ['street' => 'Via Canale 1/P']);

        $createdContact = $this->client->createContact($contact);

        self::assertNotNull($createdContact);
        self::assertEquals(5, $createdContact->getContact()->getId());
        self::assertEquals('zachary.rippin@wisozk.com', $createdContact->getContact()->getEmail());
        self::assertEquals('2022-03-15T12:01:48-05:00', $createdContact->getContact()->getCreatedAt());
        self::assertEquals('2022-03-15T12:01:48-05:00', $createdContact->getContact()->getUpdatedAt());
        self::assertEquals([], $createdContact->getFieldValues());
        self::assertCount(18, $createdContact->getContact()->getLinks());
        self::assertEquals('', $createdContact->getContact()->getOrganization());
        self::assertEquals('', $createdContact->getContact()->getOrganizationId());
    }
}
