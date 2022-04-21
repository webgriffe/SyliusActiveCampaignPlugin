<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusActiveCampaignPlugin\Client;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Request;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Serializer\SerializerInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignResourceClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ContactInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\CreateResourceResponseInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\RetrieveResourceResponseInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ListResourcesResponseInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\UpdateResourceResponseInterface;

final class ActiveCampaignResourceClientSpec extends ObjectBehavior
{
    private const CREATE_CONTACT_RESPONSE_PAYLOAD = '{"fieldValues":[{"contact":"113","field":"1","value":"The Value for First Field","cdate":"2020-08-01T10:54:59-05:00","udate":"2020-08-01T14:13:34-05:00","links":{"owner":"https://:account.api-us1.com/api/3/fieldValues/11797/owner","field":"https://:account.api-us1.com/api/3/fieldValues/11797/field"},"id":"11797","owner":"113"},{"contact":"113","field":"6","value":"2008-01-20","cdate":"2020-08-01T10:54:59-05:00","udate":"2020-08-01T14:13:34-05:00","links":{"owner":"https://:account.api-us1.com/api/3/fieldValues/11798/owner","field":"https://:account.api-us1.com/api/3/fieldValues/11798/field"},"id":"11798","owner":"113"}],"contact":{"email":"johndoe@example.com","cdate":"2018-09-28T13:50:41-05:00","udate":"2018-09-28T13:50:41-05:00","orgid":"","links":{"bounceLogs":"https://:account.api-us1.com/api/:version/contacts/113/bounceLogs","contactAutomations":"https://:account.api-us1.com/api/:version/contacts/113/contactAutomations","contactData":"https://:account.api-us1.com/api/:version/contacts/113/contactData","contactGoals":"https://:account.api-us1.com/api/:version/contacts/113/contactGoals","contactLists":"https://:account.api-us1.com/api/:version/contacts/113/contactLists","contactLogs":"https://:account.api-us1.com/api/:version/contacts/113/contactLogs","contactTags":"https://:account.api-us1.com/api/:version/contacts/113/contactTags","contactDeals":"https://:account.api-us1.com/api/:version/contacts/113/contactDeals","deals":"https://:account.api-us1.com/api/:version/contacts/113/deals","fieldValues":"https://:account.api-us1.com/api/:version/contacts/113/fieldValues","geoIps":"https://:account.api-us1.com/api/:version/contacts/113/geoIps","notes":"https://:account.api-us1.com/api/:version/contacts/113/notes","organization":"https://:account.api-us1.com/api/:version/contacts/113/organization","plusAppend":"https://:account.api-us1.com/api/:version/contacts/113/plusAppend","trackingLogs":"https://:account.api-us1.com/api/:version/contacts/113/trackingLogs","scoreValues":"https://:account.api-us1.com/api/:version/contacts/113/scoreValues"},"id":"113","organization":""}}';

    private const RETRIEVE_CONTACT_RESPONSE_PAYLOAD = '{"contactAutomations":[{"contact":"1","seriesid":"1","startid":"0","status":"0","adddate":"1976-10-16T23:23:09-05:00","remdate":null,"timespan":null,"lastblock":"0","lastdate":"1984-08-15T08:13:44-05:00","completedElements":"0","totalElements":"0","completed":0,"completeValue":100,"links":{"automation":"https://:account.api-us1.com/api/:version/contactAutomations/1/automation","contact":"https://:account.api-us1.com/api/:version/contactAutomations/1/contact","contactGoals":"https://:account.api-us1.com/api/:version/contactAutomations/1/contactGoals"},"id":"1","automation":"1"}],"contactLists":[{"contact":"1","list":"1","form":null,"seriesid":"0","sdate":null,"udate":null,"status":"1","responder":"1","sync":"0","unsubreason":null,"campaign":null,"message":null,"first_name":"John","last_name":"Doe","ip4Sub":"0","sourceid":"0","autosyncLog":null,"ip4_last":"0","ip4Unsub":"0","unsubscribeAutomation":null,"links":{"automation":"https://:account.api-us1.com/api/:version/contactLists/1/automation","list":"https://:account.api-us1.com/api/:version/contactLists/1/list","contact":"https://:account.api-us1.com/api/:version/contactLists/1/contact","form":"https://:account.api-us1.com/api/:version/contactLists/1/form","autosyncLog":"https://:account.api-us1.com/api/:version/contactLists/1/autosyncLog","campaign":"https://:account.api-us1.com/api/:version/contactLists/1/campaign","unsubscribeAutomation":"https://:account.api-us1.com/api/:version/contactLists/1/unsubscribeAutomation","message":"https://:account.api-us1.com/api/:version/contactLists/1/message"},"id":"1","automation":null}],"deals":[{"owner":"1","contact":"1","organization":null,"group":null,"title":"Consecteturomnisquoinventoremolestiaerationeamet.","nexttaskid":"0","currency":"USD","status":"0","links":{"activities":"https://:account.api-us1.com/api/:version/deals/1/activities","contact":"https://:account.api-us1.com/api/:version/deals/1/contact","contactDeals":"https://:account.api-us1.com/api/:version/deals/1/contactDeals","group":"https://:account.api-us1.com/api/:version/deals/1/group","nextTask":"https://:account.api-us1.com/api/:version/deals/1/nextTask","notes":"https://:account.api-us1.com/api/:version/deals/1/notes","organization":"https://:account.api-us1.com/api/:version/deals/1/organization","owner":"https://:account.api-us1.com/api/:version/deals/1/owner","scoreValues":"https://:account.api-us1.com/api/:version/deals/1/scoreValues","stage":"https://:account.api-us1.com/api/:version/deals/1/stage","tasks":"https://:account.api-us1.com/api/:version/deals/1/tasks"},"id":"1","nextTask":null}],"fieldValues":[{"contact":"1","field":"1","value":null,"cdate":"1981-05-16T19:02:29-05:00","udate":"1975-11-08T10:31:45-06:00","links":{"owner":"https://:account.api-us1.com/api/:version/fieldValues/1/owner","field":"https://:account.api-us1.com/api/:version/fieldValues/1/field"},"id":"1","owner":"1"}],"geoAddresses":[{"ip4":"823","country2":"AS","country":"Suriname","state":"KY","city":"NorthArnoldomouth","zip":"38704-6592","area":"0","lat":"-70.160407","lon":"-102.229406","tz":"Europe/Chisinau","tstamp":"1972-03-16T07:26:58-06:00","links":[],"id":"1"}],"geoIps":[{"contact":"1","campaignid":"1","messageid":"1","geoaddrid":"1","ip4":"0","tstamp":"1988-08-05T11:50:51-05:00","geoAddress":"1","links":{"geoAddress":"https://:account.api-us1.com/api/:version/geoIps/1/geoAddress"},"id":"1"}],"contact":{"cdate":"2007-05-05T12:49:09-05:00","email":"selmer.koss@example.com","phone":"","firstName":"Charles","lastName":"Reynolds","orgid":"0","segmentio_id":"","bounced_hard":"0","bounced_soft":"0","bounced_date":null,"ip":"0","ua":null,"hash":"","socialdata_lastcheck":null,"email_local":"","email_domain":"","sentcnt":"0","rating_tstamp":null,"gravatar":"0","deleted":"0","adate":null,"udate":null,"edate":null,"contactAutomations":["1"],"contactLists":["1"],"fieldValues":["1"],"geoIps":["1"],"deals":["1"],"accountContacts":["1"],"links":{"bounceLogs":"/1/bounceLogs","contactAutomations":"/1/contactAutomations","contactData":"/1/contactData","contactGoals":"/1/contactGoals","contactLists":"/1/contactLists","contactLogs":"/1/contactLogs","contactTags":"/1/contactTags","contactDeals":"/1/contactDeals","deals":"/1/deals","fieldValues":"/1/fieldValues","geoIps":"/1/geoIps","notes":"/1/notes","organization":"/1/organization","plusAppend":"/1/plusAppend","trackingLogs":"/1/trackingLogs","scoreValues":"/1/scoreValues"},"id":"1","organization":null}}';

    private const UPDATE_CONTACT_RESPONSE_PAYLOAD = '{"fieldValues":[{"contact":"113","field":"1","value":"TheValueforFirstField","cdate":"2020-08-01T10:54:59-05:00","udate":"2020-08-01T14:13:34-05:00","links":{"owner":"https://:account.api-us1.com/api/3/fieldValues/11797/owner","field":"https://:account.api-us1.com/api/3/fieldValues/11797/field"},"id":"11797","owner":"113"},{"contact":"113","field":"6","value":"2008-01-20","cdate":"2020-08-01T10:54:59-05:00","udate":"2020-08-01T14:13:34-05:00","links":{"owner":"https://:account.api-us1.com/api/3/fieldValues/11798/owner","field":"https://:account.api-us1.com/api/3/fieldValues/11798/field"},"id":"11798","owner":"113"}],"contact":{"cdate":"2018-09-28T13:50:41-05:00","email":"johndoe@example.com","phone":"","firstName":"John","lastName":"Doe","orgid":"0","segmentio_id":"","bounced_hard":"0","bounced_soft":"0","bounced_date":null,"ip":"0","ua":null,"hash":"8309146b50af1ed5f9cb40c7465a0315","socialdata_lastcheck":null,"email_local":"","email_domain":"","sentcnt":"0","rating_tstamp":null,"gravatar":"0","deleted":"0","anonymized":"0","adate":null,"udate":"2018-09-28T13:55:59-05:00","edate":null,"deleted_at":null,"created_utc_timestamp":"2018-09-2813:50:41","updated_utc_timestamp":"2018-09-2813:50:41","links":{"bounceLogs":"https://:account.api-us1.com/api/:version/contacts/113/bounceLogs","contactAutomations":"https://:account.api-us1.com/api/:version/contacts/113/contactAutomations","contactData":"https://:account.api-us1.com/api/:version/contacts/113/contactData","contactGoals":"https://:account.api-us1.com/api/:version/contacts/113/contactGoals","contactLists":"https://:account.api-us1.com/api/:version/contacts/113/contactLists","contactLogs":"https://:account.api-us1.com/api/:version/contacts/113/contactLogs","contactTags":"https://:account.api-us1.com/api/:version/contacts/113/contactTags","contactDeals":"https://:account.api-us1.com/api/:version/contacts/113/contactDeals","deals":"https://:account.api-us1.com/api/:version/contacts/113/deals","fieldValues":"https://:account.api-us1.com/api/:version/contacts/113/fieldValues","geoIps":"https://:account.api-us1.com/api/:version/contacts/113/geoIps","notes":"https://:account.api-us1.com/api/:version/contacts/113/notes","organization":"https://:account.api-us1.com/api/:version/contacts/113/organization","plusAppend":"https://:account.api-us1.com/api/:version/contacts/113/plusAppend","trackingLogs":"https://:account.api-us1.com/api/:version/contacts/113/trackingLogs","scoreValues":"https://:account.api-us1.com/api/:version/contacts/113/scoreValues"},"id":"113","organization":null}}';

    private const LIST_CONTACTS_RESPONSE_PAYLOAD = '{"scoreValues":[],"contacts":[{"cdate":"2022-03-24T11:23:40-05:00","email":"shop@example.com","phone":"+1-848-218-4354","firstName":"John","lastName":"Doe","orgid":"0","orgname":"","segmentio_id":"","bounced_hard":"0","bounced_soft":"0","bounced_date":null,"ip":"0","ua":null,"hash":"c368260bfe7c0fad03600117189d11ed","socialdata_lastcheck":null,"email_local":"","email_domain":"example.com","sentcnt":"0","rating_tstamp":null,"gravatar":"0","deleted":"0","anonymized":"0","adate":null,"udate":"2022-03-24T11:23:40-05:00","edate":null,"deleted_at":null,"created_utc_timestamp":"2022-03-2411:23:40","updated_utc_timestamp":"2022-03-2411:23:40","created_timestamp":"2022-03-2411:23:40","updated_timestamp":"2022-03-2411:23:40","created_by":null,"updated_by":null,"email_empty":false,"mpp_tracking":"0","scoreValues":[],"accountContacts":[],"links":{"bounceLogs":"https://webgriffe1646663336.api-us1.com/api/3/contacts/76/bounceLogs","contactAutomations":"https://webgriffe1646663336.api-us1.com/api/3/contacts/76/contactAutomations?limit=1000&orders%5Blastdate%5D=DESC","contactData":"https://webgriffe1646663336.api-us1.com/api/3/contacts/76/contactData","contactGoals":"https://webgriffe1646663336.api-us1.com/api/3/contacts/76/contactGoals","contactLists":"https://webgriffe1646663336.api-us1.com/api/3/contacts/76/contactLists","contactLogs":"https://webgriffe1646663336.api-us1.com/api/3/contacts/76/contactLogs","contactTags":"https://webgriffe1646663336.api-us1.com/api/3/contacts/76/contactTags","contactDeals":"https://webgriffe1646663336.api-us1.com/api/3/contacts/76/contactDeals","deals":"https://webgriffe1646663336.api-us1.com/api/3/contacts/76/deals","fieldValues":"https://webgriffe1646663336.api-us1.com/api/3/contacts/76/fieldValues","geoIps":"https://webgriffe1646663336.api-us1.com/api/3/contacts/76/geoIps","notes":"https://webgriffe1646663336.api-us1.com/api/3/contacts/76/notes","organization":"https://webgriffe1646663336.api-us1.com/api/3/contacts/76/organization","plusAppend":"https://webgriffe1646663336.api-us1.com/api/3/contacts/76/plusAppend","trackingLogs":"https://webgriffe1646663336.api-us1.com/api/3/contacts/76/trackingLogs","scoreValues":"https://webgriffe1646663336.api-us1.com/api/3/contacts/76/scoreValues","accountContacts":"https://webgriffe1646663336.api-us1.com/api/3/contacts/76/accountContacts","automationEntryCounts":"https://webgriffe1646663336.api-us1.com/api/3/contacts/76/automationEntryCounts"},"id":"76","organization":null}],"meta":{"page_input":{"segmentid":0,"formid":0,"listid":0,"tagid":0,"limit":20,"offset":0,"search":null,"sort":null,"seriesid":0,"waitid":0,"status":-1,"forceQuery":0,"cacheid":"60d276cba96b2e2c64db05a694810408","email":"shop@example.com"},"total":"1","sortable":true}}';

    public function let(
        ClientInterface $httpClient,
        SerializerInterface $serializer,
        ContactInterface $contact,
        ResponseInterface $response
    ): void {
        $this->beConstructedWith(
            $httpClient,
            $serializer,
            'contact',
            'Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Contact\ContactResponse',
            'Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Contact\CreateContactResponse',
            'Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Contact\RetrieveContactResponse',
            'Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Contact\ListContactsResponse',
            'Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Contact\UpdateContactResponse'
        );

        $serializer->serialize(['contact' => $contact], 'json')->willReturn('{"contact":{"email":"johndoe@example.com","firstName":"John","lastName":"Doe","phone":"7223224241","fieldValues":[{"field":"1","value":"The Value for First Field"},{"field":"6","value":"2008-01-20"}]}}');

        $httpClient->send(Argument::type(Request::class))->willReturn($response);
    }

    public function it_implements_interface(): void
    {
        $this->shouldImplement(ActiveCampaignResourceClientInterface::class);
    }

    public function it_creates_a_resource_on_active_campaign(
        SerializerInterface $serializer,
        ResponseInterface $response,
        StreamInterface $responseBody,
        ContactInterface $contact,
        CreateResourceResponseInterface $createResourceResponse
    ): void {
        $response->getStatusCode()->willReturn(201);
        $response->getBody()->willReturn($responseBody);
        $responseBody->getContents()->willReturn(self::CREATE_CONTACT_RESPONSE_PAYLOAD);
        $serializer->deserialize(
            self::CREATE_CONTACT_RESPONSE_PAYLOAD,
            'Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Contact\CreateContactResponse',
            'json',
            ['resource' => 'contact']
        )->shouldBeCalledOnce()->willReturn($createResourceResponse);

        $this->create($contact)->shouldReturn($createResourceResponse);
    }

    public function it_throws_while_creating_a_resource_when_the_response_is_not_found(
        ResponseInterface $response,
        ContactInterface $contact,
        StreamInterface $stream
    ): void {
        $response->getStatusCode()->willReturn(404);
        $response->getBody()->willReturn($stream);
        $stream->getContents()->willReturn('{"message":"No Result found for Subscriber with id 1"}');

        $this->shouldThrow(new NotFoundHttpException('No Result found for Subscriber with id 1'))->during('create', [$contact]);
    }

    public function it_throws_while_creating_a_resource_when_the_response_is_not_processable(
        ResponseInterface $response,
        ContactInterface $contact,
        StreamInterface $stream
    ): void {
        $response->getStatusCode()->willReturn(422);
        $response->getBody()->willReturn($stream);
        $stream->getContents()->willReturn('{"errors":[{"title":"Email address already exists in the system","detail":"","code":"duplicate","source":{"pointer":"/data/attributes/email"}}]}');

        $this->shouldThrow(new UnprocessableEntityHttpException('Email address already exists in the system'))->during('create', [$contact]);
    }

    public function it_throws_while_creating_a_resource_when_the_response_is_not_recognized(
        ResponseInterface $response,
        ContactInterface $contact
    ): void {
        $response->getStatusCode()->willReturn(500);
        $response->getHeaders()->willReturn([]);
        $response->getReasonPhrase()->willReturn('Internal Server Error');

        $this->shouldThrow(new HttpException(500, 'Internal Server Error'))->during('create', [$contact]);
    }

    public function it_retrieves_a_resource_from_active_campaign(
        SerializerInterface $serializer,
        ResponseInterface $response,
        StreamInterface $responseBody,
        RetrieveResourceResponseInterface $retrieveResourceResponse
    ): void {
        $response->getStatusCode()->willReturn(200);
        $response->getBody()->willReturn($responseBody);
        $responseBody->getContents()->willReturn(self::RETRIEVE_CONTACT_RESPONSE_PAYLOAD);
        $serializer->deserialize(
            self::RETRIEVE_CONTACT_RESPONSE_PAYLOAD,
            'Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Contact\RetrieveContactResponse',
            'json',
            ['resource' => 'contact']
        )->shouldBeCalledOnce()->willReturn($retrieveResourceResponse);

        $this->get(113)->shouldReturn($retrieveResourceResponse);
    }

    public function it_throws_while_retrieving_a_resource_when_the_response_is_not_found(
        ResponseInterface $response,
        StreamInterface $stream
    ): void {
        $response->getStatusCode()->willReturn(404);
        $response->getBody()->willReturn($stream);
        $response->getHeaders()->willReturn([]);
        $stream->getContents()->willReturn('{"message":"No Result found for Subscriber with id 1"}');

        $this->shouldThrow(new NotFoundHttpException('No Result found for Subscriber with id 1'))->during('get', [1]);
    }

    public function it_throws_while_retrieving_a_resource_when_the_response_is_not_recognized(
        ResponseInterface $response
    ): void {
        $response->getStatusCode()->willReturn(500);
        $response->getHeaders()->willReturn([]);
        $response->getReasonPhrase()->willReturn('Internal Server Error');

        $this->shouldThrow(new HttpException(500, 'Internal Server Error'))->during('get', [113]);
    }

    public function it_lists_resources_on_active_campaign_filtered_by_query_params(
        SerializerInterface $serializer,
        ResponseInterface $response,
        StreamInterface $responseBody,
        ListResourcesResponseInterface $listResourcesResponse
    ): void {
        $response->getStatusCode()->willReturn(200);
        $response->getBody()->willReturn($responseBody);
        $responseBody->getContents()->willReturn(self::LIST_CONTACTS_RESPONSE_PAYLOAD);
        $serializer->deserialize(
            self::LIST_CONTACTS_RESPONSE_PAYLOAD,
            'Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Contact\ListContactsResponse',
            'json',
            [
                'resource' => 'contact',
                'responseType' => 'Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Contact\ContactResponse',
                'type' => ListResourcesResponseInterface::class,
            ]
        )->shouldBeCalledOnce()->willReturn($listResourcesResponse);

        $this->list(['email' => 'info@test.com'])->shouldReturn($listResourcesResponse);
    }

    public function it_throws_while_listing_resources_when_the_request_is_bad(
        ResponseInterface $response
    ): void {
        $response->getStatusCode()->willReturn(400);
        $response->getHeaders()->willReturn([]);
        $response->getReasonPhrase()->willReturn('The field "email2" does not exists!');

        $this->shouldThrow(new BadRequestHttpException('The field "email2" does not exists!'))->during('list', [['email2' => 'info@test.com']]);
    }

    public function it_throws_while_listing_resources_when_the_response_is_not_recognized(
        ResponseInterface $response,
        ContactInterface $contact
    ): void {
        $response->getStatusCode()->willReturn(500);
        $response->getHeaders()->willReturn([]);
        $response->getReasonPhrase()->willReturn('Internal Server Error');

        $this->shouldThrow(new HttpException(500, 'Internal Server Error'))->during('list', [['email' => 'info@test.com']]);
    }

    public function it_updates_a_resource_on_active_campaign(
        SerializerInterface $serializer,
        ResponseInterface $response,
        StreamInterface $responseBody,
        ContactInterface $contact,
        UpdateResourceResponseInterface $updateResourceResponse
    ): void {
        $response->getStatusCode()->willReturn(200);
        $response->getBody()->willReturn($responseBody);
        $responseBody->getContents()->willReturn(self::UPDATE_CONTACT_RESPONSE_PAYLOAD);
        $serializer->deserialize(
            self::UPDATE_CONTACT_RESPONSE_PAYLOAD,
            'Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Contact\UpdateContactResponse',
            'json',
            ['resource' => 'contact']
        )->shouldBeCalledOnce()->willReturn($updateResourceResponse);

        $this->update(113, $contact)->shouldReturn($updateResourceResponse);
    }

    public function it_throws_while_updating_a_resource_when_the_response_is_not_found(
        ResponseInterface $response,
        ContactInterface $contact,
        StreamInterface $stream
    ): void {
        $response->getStatusCode()->willReturn(404);
        $response->getBody()->willReturn($stream);
        $stream->getContents()->willReturn('{"message":"No Result found for Subscriber with id 1"}');

        $this->shouldThrow(new NotFoundHttpException('No Result found for Subscriber with id 1'))->during('update', [1, $contact]);
    }

    public function it_throws_while_updating_a_resource_when_the_response_is_not_recognized(
        ResponseInterface $response,
        ContactInterface $contact
    ): void {
        $response->getStatusCode()->willReturn(500);
        $response->getHeaders()->willReturn([]);
        $response->getReasonPhrase()->willReturn('Internal Server Error');

        $this->shouldThrow(new HttpException(500, 'Internal Server Error'))->during('update', [113, $contact]);
    }

    public function it_removes_a_resource_on_active_campaign(
        ResponseInterface $response,
        StreamInterface $responseBody,
    ): void {
        $response->getStatusCode()->willReturn(200);
        $response->getBody()->willReturn($responseBody);
        $responseBody->getContents()->willReturn('{}');

        $this->remove(113)->shouldReturn(null);
    }

    public function it_throws_while_removing_a_resource_when_the_response_is_not_found(
        ResponseInterface $response,
        StreamInterface $stream
    ): void {
        $response->getStatusCode()->willReturn(404);
        $response->getBody()->willReturn($stream);
        $stream->getContents()->willReturn('{"message":"No Result found for Subscriber with id 1"}');

        $this->shouldThrow(new NotFoundHttpException('No Result found for Subscriber with id 1'))->during('remove', [1]);
    }

    public function it_throws_while_removing_a_resource_when_the_response_is_not_recognized(
        ResponseInterface $response
    ): void {
        $response->getStatusCode()->willReturn(500);
        $response->getHeaders()->willReturn([]);
        $response->getReasonPhrase()->willReturn('Internal Server Error');

        $this->shouldThrow(new HttpException(500, 'Internal Server Error'))->during('remove', [113]);
    }
}
