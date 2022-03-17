<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusActiveCampaignPlugin\Integration\Client;

use Psr\Http\Message\RequestInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Webgriffe\SyliusActiveCampaignPlugin\Stub\HttpClientStub;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignResourceClient;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\Contact;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\FieldValue;

final class ActiveCampaignClientTest extends KernelTestCase
{
    private ActiveCampaignResourceClient $client;

    protected function setUp(): void
    {
        parent::setUp();
        HttpClientStub::setUp();

        $this->client = self::getContainer()->get('webgriffe.sylius_active_campaign_plugin.client.active_campaign.contact');
    }

    public function test_it_creates_contact_on_active_campaign(): void
    {
        HttpClientStub::$responseStatusCode = 201;
        HttpClientStub::$responseBodyContent = '{"fieldValues":[{"contact":"113","field":"1","value":"The Value for First Field","cdate":"2020-08-01T10:54:59-05:00","udate":"2020-08-01T14:13:34-05:00","links":{"owner":"https://:account.api-us1.com/api/3/fieldValues/11797/owner","field":"https://:account.api-us1.com/api/3/fieldValues/11797/field"},"id":"11797","owner":"113"},{"contact":"113","field":"6","value":"2008-01-20","cdate":"2020-08-01T10:54:59-05:00","udate":"2020-08-01T14:13:34-05:00","links":{"owner":"https://:account.api-us1.com/api/3/fieldValues/11798/owner","field":"https://:account.api-us1.com/api/3/fieldValues/11798/field"},"id":"11798","owner":"113"}],"contact":{"email":"johndoe@example.com","cdate":"2018-09-28T13:50:41-05:00","udate":"2018-09-28T13:50:41-05:00","orgid":"","links":{"bounceLogs":"https://:account.api-us1.com/api/:version/contacts/113/bounceLogs","contactAutomations":"https://:account.api-us1.com/api/:version/contacts/113/contactAutomations","contactData":"https://:account.api-us1.com/api/:version/contacts/113/contactData","contactGoals":"https://:account.api-us1.com/api/:version/contacts/113/contactGoals","contactLists":"https://:account.api-us1.com/api/:version/contacts/113/contactLists","contactLogs":"https://:account.api-us1.com/api/:version/contacts/113/contactLogs","contactTags":"https://:account.api-us1.com/api/:version/contacts/113/contactTags","contactDeals":"https://:account.api-us1.com/api/:version/contacts/113/contactDeals","deals":"https://:account.api-us1.com/api/:version/contacts/113/deals","fieldValues":"https://:account.api-us1.com/api/:version/contacts/113/fieldValues","geoIps":"https://:account.api-us1.com/api/:version/contacts/113/geoIps","notes":"https://:account.api-us1.com/api/:version/contacts/113/notes","organization":"https://:account.api-us1.com/api/:version/contacts/113/organization","plusAppend":"https://:account.api-us1.com/api/:version/contacts/113/plusAppend","trackingLogs":"https://:account.api-us1.com/api/:version/contacts/113/trackingLogs","scoreValues":"https://:account.api-us1.com/api/:version/contacts/113/scoreValues"},"id":"113","organization":""}}';
        $contact = new Contact('johndoe@example.com', 'John', 'Doe', '7223224241', [new FieldValue('1', 'The Value for First Field'), new FieldValue('6', '2008-01-20')]);

        $createdContact = $this->client->create($contact);

        self::assertCount(1, HttpClientStub::$sendedRequests);
        $sendedRequest = reset(HttpClientStub::$sendedRequests);
        self::assertInstanceOf(RequestInterface::class, $sendedRequest);
        self::assertEquals('/api/3/contacts', $sendedRequest->getUri()->getPath());
        self::assertEquals('POST', $sendedRequest->getMethod());
        self::assertEquals('{"contact":{"email":"johndoe@example.com","firstName":"John","lastName":"Doe","phone":"7223224241","fieldValues":[{"field":"1","value":"The Value for First Field"},{"field":"6","value":"2008-01-20"}]}}', $sendedRequest->getBody()->getContents());

        self::assertNotNull($createdContact);
        self::assertEquals(113, $createdContact->getContact()->getId());
        self::assertEquals('johndoe@example.com', $createdContact->getContact()->getEmail());
        self::assertEquals('2018-09-28T13:50:41-05:00', $createdContact->getContact()->getCreatedAt());
        self::assertEquals('2018-09-28T13:50:41-05:00', $createdContact->getContact()->getUpdatedAt());
        self::assertCount(2, $createdContact->getFieldValues());
        self::assertCount(16, $createdContact->getContact()->getLinks());
        self::assertEquals('', $createdContact->getContact()->getOrganization());
        self::assertEquals('', $createdContact->getContact()->getOrganizationId());
    }

    public function test_it_updates_contact_on_active_campaign(): void
    {
        HttpClientStub::$responseStatusCode = 200;
        HttpClientStub::$responseBodyContent = '{"fieldValues":[{"contact":"113","field":"1","value":"The Value for First Field","cdate":"2020-08-01T10:54:59-05:00","udate":"2020-08-01T14:13:34-05:00","links":{"owner":"https://:account.api-us1.com/api/3/fieldValues/11797/owner","field":"https://:account.api-us1.com/api/3/fieldValues/11797/field"},"id":"11797","owner":"113"},{"contact":"113","field":"6","value":"2008-01-20","cdate":"2020-08-01T10:54:59-05:00","udate":"2020-08-01T14:13:34-05:00","links":{"owner":"https://:account.api-us1.com/api/3/fieldValues/11798/owner","field":"https://:account.api-us1.com/api/3/fieldValues/11798/field"},"id":"11798","owner":"113"}],"contact":{"cdate":"2018-09-28T13:50:41-05:00","email":"johndoe@example.com","phone":"","firstName":"John","lastName":"Doe","orgid":"0","segmentio_id":"","bounced_hard":"0","bounced_soft":"0","bounced_date":null,"ip":"0","ua":null,"hash":"8309146b50af1ed5f9cb40c7465a0315","socialdata_lastcheck":null,"email_local":"","email_domain":"","sentcnt":"0","rating_tstamp":null,"gravatar":"0","deleted":"0","anonymized":"0","adate":null,"udate":"2018-09-28T13:55:59-05:00","edate":null,"deleted_at":null,"created_utc_timestamp":"2018-09-28 13:50:41","updated_utc_timestamp":"2018-09-28 13:50:41","links":{"bounceLogs":"https://:account.api-us1.com/api/:version/contacts/113/bounceLogs","contactAutomations":"https://:account.api-us1.com/api/:version/contacts/113/contactAutomations","contactData":"https://:account.api-us1.com/api/:version/contacts/113/contactData","contactGoals":"https://:account.api-us1.com/api/:version/contacts/113/contactGoals","contactLists":"https://:account.api-us1.com/api/:version/contacts/113/contactLists","contactLogs":"https://:account.api-us1.com/api/:version/contacts/113/contactLogs","contactTags":"https://:account.api-us1.com/api/:version/contacts/113/contactTags","contactDeals":"https://:account.api-us1.com/api/:version/contacts/113/contactDeals","deals":"https://:account.api-us1.com/api/:version/contacts/113/deals","fieldValues":"https://:account.api-us1.com/api/:version/contacts/113/fieldValues","geoIps":"https://:account.api-us1.com/api/:version/contacts/113/geoIps","notes":"https://:account.api-us1.com/api/:version/contacts/113/notes","organization":"https://:account.api-us1.com/api/:version/contacts/113/organization","plusAppend":"https://:account.api-us1.com/api/:version/contacts/113/plusAppend","trackingLogs":"https://:account.api-us1.com/api/:version/contacts/113/trackingLogs","scoreValues":"https://:account.api-us1.com/api/:version/contacts/113/scoreValues"},"id":"113","organization":null}}';
        $contact = new Contact('johndoe@example.com', 'John', 'Doe', '7223224241', [new FieldValue('1', 'The Value for First Field'), new FieldValue('6', '2008-01-20')]);

        $updatedContact = $this->client->update(113, $contact);

        self::assertCount(1, HttpClientStub::$sendedRequests);
        $sendedRequest = reset(HttpClientStub::$sendedRequests);
        self::assertInstanceOf(RequestInterface::class, $sendedRequest);
        self::assertEquals('/api/3/contacts/113', $sendedRequest->getUri()->getPath());
        self::assertEquals('PUT', $sendedRequest->getMethod());
        self::assertEquals('{"contact":{"email":"johndoe@example.com","firstName":"John","lastName":"Doe","phone":"7223224241","fieldValues":[{"field":"1","value":"The Value for First Field"},{"field":"6","value":"2008-01-20"}]}}', $sendedRequest->getBody()->getContents());

        self::assertNotNull($updatedContact);
        self::assertCount(2, $updatedContact->getFieldValues());
        self::assertEquals(113, $updatedContact->getContact()->getId());
        self::assertEquals('2018-09-28T13:50:41-05:00', $updatedContact->getContact()->getCreatedAt());
        self::assertEquals('johndoe@example.com', $updatedContact->getContact()->getEmail());
        self::assertEquals('', $updatedContact->getContact()->getPhone());
        self::assertEquals('John', $updatedContact->getContact()->getFirstName());
        self::assertEquals('Doe', $updatedContact->getContact()->getLastName());
        self::assertEquals('0', $updatedContact->getContact()->getOrganizationId());
        self::assertEquals('', $updatedContact->getContact()->getSegmentioId());
        self::assertEquals('0', $updatedContact->getContact()->getBouncedHard());
        self::assertEquals('0', $updatedContact->getContact()->getBouncedSoft());
        self::assertEquals('0', $updatedContact->getContact()->getIp());
        self::assertEquals('8309146b50af1ed5f9cb40c7465a0315', $updatedContact->getContact()->getHash());
        self::assertEquals('', $updatedContact->getContact()->getEmailLocal());
        self::assertEquals('', $updatedContact->getContact()->getEmailDomain());
        self::assertEquals('0', $updatedContact->getContact()->getSentCnt());
        self::assertEquals('0', $updatedContact->getContact()->getGravatar());
        self::assertEquals('0', $updatedContact->getContact()->getDeleted());
        self::assertEquals('0', $updatedContact->getContact()->getAnonymized());
        self::assertEquals('2018-09-28T13:55:59-05:00', $updatedContact->getContact()->getUpdatedAt());
        self::assertEquals('2018-09-28 13:50:41', $updatedContact->getContact()->getCreatedAtUTCTimestamp());
        self::assertEquals('2018-09-28 13:50:41', $updatedContact->getContact()->getUpdatedAtUTCTimestamp());
        self::assertCount(16, $updatedContact->getContact()->getLinks());
        self::assertEquals(113, $updatedContact->getContact()->getId());
        self::assertNull($updatedContact->getContact()->getBouncedDate());
        self::assertNull($updatedContact->getContact()->getUa());
        self::assertNull($updatedContact->getContact()->getSocialDataLastCheck());
        self::assertNull($updatedContact->getContact()->getRatingTimestamp());
        self::assertNull($updatedContact->getContact()->getADate());
        self::assertNull($updatedContact->getContact()->getEDate());
        self::assertNull($updatedContact->getContact()->getDeletedAt());
        self::assertNull($updatedContact->getContact()->getOrganization());
    }

    public function test_it_removes_contact_on_active_campaign(): void
    {
        HttpClientStub::$responseStatusCode = 200;
        HttpClientStub::$responseBodyContent = '{}';

        $this->client->remove(113);

        self::assertCount(1, HttpClientStub::$sendedRequests);
        $sendedRequest = reset(HttpClientStub::$sendedRequests);
        self::assertInstanceOf(RequestInterface::class, $sendedRequest);
        self::assertEquals('/api/3/contacts/113', $sendedRequest->getUri()->getPath());
        self::assertEquals('DELETE', $sendedRequest->getMethod());
        self::assertEmpty($sendedRequest->getBody()->getContents());
    }
}
