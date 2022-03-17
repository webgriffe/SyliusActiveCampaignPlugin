<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusActiveCampaignPlugin\Client;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Request;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Serializer\SerializerInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignResourceClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ContactInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\CreateResourceResponseInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\UpdateResourceResponseInterface;

final class ActiveCampaignResourceClientSpec extends ObjectBehavior
{
    private const CREATE_CONTACT_REQUEST_PAYLOAD = '{"contact":{"email":"johndoe@example.com","firstName":"John","lastName":"Doe","phone":"7223224241","fieldValues":[{"field":"1","value":"The Value for First Field"},{"field":"6","value":"2008-01-20"}]}}';

    private const CREATE_CONTACT_RESPONSE_PAYLOAD = '{"fieldValues":[{"contact":"113","field":"1","value":"The Value for First Field","cdate":"2020-08-01T10:54:59-05:00","udate":"2020-08-01T14:13:34-05:00","links":{"owner":"https://:account.api-us1.com/api/3/fieldValues/11797/owner","field":"https://:account.api-us1.com/api/3/fieldValues/11797/field"},"id":"11797","owner":"113"},{"contact":"113","field":"6","value":"2008-01-20","cdate":"2020-08-01T10:54:59-05:00","udate":"2020-08-01T14:13:34-05:00","links":{"owner":"https://:account.api-us1.com/api/3/fieldValues/11798/owner","field":"https://:account.api-us1.com/api/3/fieldValues/11798/field"},"id":"11798","owner":"113"}],"contact":{"email":"johndoe@example.com","cdate":"2018-09-28T13:50:41-05:00","udate":"2018-09-28T13:50:41-05:00","orgid":"","links":{"bounceLogs":"https://:account.api-us1.com/api/:version/contacts/113/bounceLogs","contactAutomations":"https://:account.api-us1.com/api/:version/contacts/113/contactAutomations","contactData":"https://:account.api-us1.com/api/:version/contacts/113/contactData","contactGoals":"https://:account.api-us1.com/api/:version/contacts/113/contactGoals","contactLists":"https://:account.api-us1.com/api/:version/contacts/113/contactLists","contactLogs":"https://:account.api-us1.com/api/:version/contacts/113/contactLogs","contactTags":"https://:account.api-us1.com/api/:version/contacts/113/contactTags","contactDeals":"https://:account.api-us1.com/api/:version/contacts/113/contactDeals","deals":"https://:account.api-us1.com/api/:version/contacts/113/deals","fieldValues":"https://:account.api-us1.com/api/:version/contacts/113/fieldValues","geoIps":"https://:account.api-us1.com/api/:version/contacts/113/geoIps","notes":"https://:account.api-us1.com/api/:version/contacts/113/notes","organization":"https://:account.api-us1.com/api/:version/contacts/113/organization","plusAppend":"https://:account.api-us1.com/api/:version/contacts/113/plusAppend","trackingLogs":"https://:account.api-us1.com/api/:version/contacts/113/trackingLogs","scoreValues":"https://:account.api-us1.com/api/:version/contacts/113/scoreValues"},"id":"113","organization":""}}';

    private const UPDATE_CONTACT_RESPONSE_PAYLOAD = '{"fieldValues":[{"contact":"113","field":"1","value":"TheValueforFirstField","cdate":"2020-08-01T10:54:59-05:00","udate":"2020-08-01T14:13:34-05:00","links":{"owner":"https://:account.api-us1.com/api/3/fieldValues/11797/owner","field":"https://:account.api-us1.com/api/3/fieldValues/11797/field"},"id":"11797","owner":"113"},{"contact":"113","field":"6","value":"2008-01-20","cdate":"2020-08-01T10:54:59-05:00","udate":"2020-08-01T14:13:34-05:00","links":{"owner":"https://:account.api-us1.com/api/3/fieldValues/11798/owner","field":"https://:account.api-us1.com/api/3/fieldValues/11798/field"},"id":"11798","owner":"113"}],"contact":{"cdate":"2018-09-28T13:50:41-05:00","email":"johndoe@example.com","phone":"","firstName":"John","lastName":"Doe","orgid":"0","segmentio_id":"","bounced_hard":"0","bounced_soft":"0","bounced_date":null,"ip":"0","ua":null,"hash":"8309146b50af1ed5f9cb40c7465a0315","socialdata_lastcheck":null,"email_local":"","email_domain":"","sentcnt":"0","rating_tstamp":null,"gravatar":"0","deleted":"0","anonymized":"0","adate":null,"udate":"2018-09-28T13:55:59-05:00","edate":null,"deleted_at":null,"created_utc_timestamp":"2018-09-2813:50:41","updated_utc_timestamp":"2018-09-2813:50:41","links":{"bounceLogs":"https://:account.api-us1.com/api/:version/contacts/113/bounceLogs","contactAutomations":"https://:account.api-us1.com/api/:version/contacts/113/contactAutomations","contactData":"https://:account.api-us1.com/api/:version/contacts/113/contactData","contactGoals":"https://:account.api-us1.com/api/:version/contacts/113/contactGoals","contactLists":"https://:account.api-us1.com/api/:version/contacts/113/contactLists","contactLogs":"https://:account.api-us1.com/api/:version/contacts/113/contactLogs","contactTags":"https://:account.api-us1.com/api/:version/contacts/113/contactTags","contactDeals":"https://:account.api-us1.com/api/:version/contacts/113/contactDeals","deals":"https://:account.api-us1.com/api/:version/contacts/113/deals","fieldValues":"https://:account.api-us1.com/api/:version/contacts/113/fieldValues","geoIps":"https://:account.api-us1.com/api/:version/contacts/113/geoIps","notes":"https://:account.api-us1.com/api/:version/contacts/113/notes","organization":"https://:account.api-us1.com/api/:version/contacts/113/organization","plusAppend":"https://:account.api-us1.com/api/:version/contacts/113/plusAppend","trackingLogs":"https://:account.api-us1.com/api/:version/contacts/113/trackingLogs","scoreValues":"https://:account.api-us1.com/api/:version/contacts/113/scoreValues"},"id":"113","organization":null}}';

    public function let(
        ClientInterface $httpClient,
        SerializerInterface $deserializer,
        SerializerInterface $serializer,
        ContactInterface $contact,
        ResponseInterface $response
    ): void {
        $this->beConstructedWith($httpClient, $serializer, $deserializer, 'contact');

        $serializer->serialize(['contact' => $contact], 'json')->willReturn(self::CREATE_CONTACT_REQUEST_PAYLOAD);

        $httpClient->send(Argument::type(Request::class))->willReturn($response);
    }

    public function it_implements_interface(): void
    {
        $this->shouldImplement(ActiveCampaignResourceClientInterface::class);
    }

    public function it_creates_a_resource_on_active_campaign(
        SerializerInterface $deserializer,
        ResponseInterface $response,
        StreamInterface $responseBody,
        ContactInterface $contact,
        CreateResourceResponseInterface $createResourceResponse
    ): void {
        $response->getStatusCode()->willReturn(201);
        $response->getBody()->willReturn($responseBody);
        $responseBody->getContents()->willReturn(self::CREATE_CONTACT_RESPONSE_PAYLOAD);
        $deserializer->deserialize(self::CREATE_CONTACT_RESPONSE_PAYLOAD, CreateResourceResponseInterface::class, 'json', ['resource' => 'contact'])->shouldBeCalledOnce()->willReturn($createResourceResponse);

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

    public function it_updates_a_resource_on_active_campaign(
        SerializerInterface $deserializer,
        ResponseInterface $response,
        StreamInterface $responseBody,
        ContactInterface $contact,
        UpdateResourceResponseInterface $updateResourceResponse
    ): void {
        $response->getStatusCode()->willReturn(200);
        $response->getBody()->willReturn($responseBody);
        $responseBody->getContents()->willReturn(self::UPDATE_CONTACT_RESPONSE_PAYLOAD);
        $deserializer->deserialize(self::UPDATE_CONTACT_RESPONSE_PAYLOAD, UpdateResourceResponseInterface::class, 'json', ['resource' => 'contact'])->shouldBeCalledOnce()->willReturn($updateResourceResponse);

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
