<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusActiveCampaignPlugin\Integration\Client;

use Psr\Http\Message\RequestInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Webgriffe\SyliusActiveCampaignPlugin\Stub\HttpClientStub;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignResourceClient;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ContactList;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ChannelCustomerInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ContactList\CreateContactListResponse;

final class ActiveCampaignContactListClientTest extends KernelTestCase
{
    private ActiveCampaignResourceClient $client;

    protected function setUp(): void
    {
        parent::setUp();
        HttpClientStub::setUp();

        $this->client = self::getContainer()->get('webgriffe.sylius_active_campaign_plugin.client.active_campaign.contact_list');
    }

    public function test_it_creates_contact_list_on_active_campaign(): void
    {
        HttpClientStub::$responseStatusCode = 201;
        HttpClientStub::$responseBodyContent = '{"contacts":[{"cdate":"2017-07-24T12:09:52-05:00","email":"johndoe@example.com","phone":"","firstName":"John","lastName":"Doe","orgid":"0","segmentio_id":"","bounced_hard":"0","bounced_soft":"0","bounced_date":"0000-00-00","ip":"0","ua":"","hash":"1234567890","socialdata_lastcheck":"0000-00-0000:00:00","email_local":"","email_domain":"","sentcnt":"1","rating_tstamp":"0000-00-00","gravatar":"0","deleted":"0","anonymized":"0","adate":"2018-10-16T13:52:32-05:00","udate":"2018-10-16T13:50:18-05:00","deleted_at":"0000-00-0000:00:00","created_utc_timestamp":"2018-10-0108:40:10","updated_utc_timestamp":"2018-10-1613:50:18","links":{"bounceLogs":"https://:account.api-us1.com/api/:version/contacts/1/bounceLogs","contactAutomations":"https://:account.api-us1.com/api/:version/contacts/1/contactAutomations","contactData":"https://:account.api-us1.com/api/:version/contacts/1/contactData","contactGoals":"https://:account.api-us1.com/api/:version/contacts/1/contactGoals","contactLists":"https://:account.api-us1.com/api/:version/contacts/1/contactLists","contactLogs":"https://:account.api-us1.com/api/:version/contacts/1/contactLogs","contactTags":"https://:account.api-us1.com/api/:version/contacts/1/contactTags","contactDeals":"https://:account.api-us1.com/api/:version/contacts/1/contactDeals","deals":"https://staging-tjahn.api-us1.com/api/3/contacts/1/deals","fieldValues":"https://:account.api-us1.com/api/:version/contacts/1/fieldValues","geoIps":"https://:account.api-us1.com/api/:version/contacts/1/geoIps","notes":"https://:account.api-us1.com/api/:version/contacts/1/notes","organization":"https://:account.api-us1.com/api/:version/contacts/1/organization","plusAppend":"https://:account.api-us1.com/api/:version/contacts/1/plusAppend","trackingLogs":"https://:account.api-us1.com/api/:version/contacts/1/trackingLogs","scoreValues":"https://:account.api-us1.com/api/:version/contacts/1/scoreValues"},"id":"1","organization":null}],"contactList":{"contact":"1","list":"2","form":null,"seriesid":"0","sdate":"2018-10-16T13:52:35-05:00","status":1,"responder":"1","sync":"0","unsubreason":"","campaign":null,"message":null,"first_name":"John","last_name":"Doe","ip4Sub":"0","sourceid":"3","autosyncLog":null,"ip4_last":"0","ip4Unsub":"0","unsubscribeAutomation":null,"links":{"automation":"https://:account.api-us1.com/api/:version/contactLists/2/automation","list":"https://:account.api-us1.com/api/:version/contactLists/2/list","contact":"https://:account.api-us1.com/api/:version/contactLists/2/contact","form":"https://:account.api-us1.com/api/:version/contactLists/2/form","autosyncLog":"https://:account.api-us1.com/api/:version/contactLists/2/autosyncLog","campaign":"https://:account.api-us1.com/api/:version/contactLists/2/campaign","unsubscribeAutomation":"https://:account.api-us1.com/api/:version/contactLists/2/unsubscribeAutomation","message":"https://:account.api-us1.com/api/:version/contactLists/2/message"},"id":"2","automation":null}}';
        $contactList = new ContactList(4, 1, ChannelCustomerInterface::SUBSCRIBED_TO_CONTACT_LIST, null);

        $createdContactList = $this->client->create($contactList);

        self::assertCount(1, HttpClientStub::$sentRequests);
        $sentRequest = reset(HttpClientStub::$sentRequests);
        self::assertInstanceOf(RequestInterface::class, $sentRequest);
        self::assertEquals('/api/3/contactLists', $sentRequest->getUri()->getPath());
        self::assertEquals('POST', $sentRequest->getMethod());
        self::assertEquals('{"contactList":{"list":4,"contact":1,"status":1,"sourceid":null}}', $sentRequest->getBody()->getContents());

        self::assertNotNull($createdContactList);
        self::assertInstanceOf(CreateContactListResponse::class, $createdContactList);
        self::assertEquals(2, $createdContactList->getResourceResponse()->getId());
    }
}
