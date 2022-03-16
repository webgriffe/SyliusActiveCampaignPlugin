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
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ContactInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ContactResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\CreateContactResponse;

final class ActiveCampaignClientSpec extends ObjectBehavior
{
    private const CREATE_CONTACT_REQUEST_PAYLOAD = '{"contact":{"email":"johndoe@example.com","firstName":"John","lastName":"Doe","phone":"7223224241","fieldValues":[{"field":"1","value":"The Value for First Field"},{"field":"6","value":"2008-01-20"}]}}';

    private const CREATE_CONTACT_RESPONSE_PAYLOAD = '{"fieldValues":[{"contact":"113","field":"1","value":"The Value for First Field","cdate":"2020-08-01T10:54:59-05:00","udate":"2020-08-01T14:13:34-05:00","links":{"owner":"https://:account.api-us1.com/api/3/fieldValues/11797/owner","field":"https://:account.api-us1.com/api/3/fieldValues/11797/field"},"id":"11797","owner":"113"},{"contact":"113","field":"6","value":"2008-01-20","cdate":"2020-08-01T10:54:59-05:00","udate":"2020-08-01T14:13:34-05:00","links":{"owner":"https://:account.api-us1.com/api/3/fieldValues/11798/owner","field":"https://:account.api-us1.com/api/3/fieldValues/11798/field"},"id":"11798","owner":"113"}],"contact":{"email":"johndoe@example.com","cdate":"2018-09-28T13:50:41-05:00","udate":"2018-09-28T13:50:41-05:00","orgid":"","links":{"bounceLogs":"https://:account.api-us1.com/api/:version/contacts/113/bounceLogs","contactAutomations":"https://:account.api-us1.com/api/:version/contacts/113/contactAutomations","contactData":"https://:account.api-us1.com/api/:version/contacts/113/contactData","contactGoals":"https://:account.api-us1.com/api/:version/contacts/113/contactGoals","contactLists":"https://:account.api-us1.com/api/:version/contacts/113/contactLists","contactLogs":"https://:account.api-us1.com/api/:version/contacts/113/contactLogs","contactTags":"https://:account.api-us1.com/api/:version/contacts/113/contactTags","contactDeals":"https://:account.api-us1.com/api/:version/contacts/113/contactDeals","deals":"https://:account.api-us1.com/api/:version/contacts/113/deals","fieldValues":"https://:account.api-us1.com/api/:version/contacts/113/fieldValues","geoIps":"https://:account.api-us1.com/api/:version/contacts/113/geoIps","notes":"https://:account.api-us1.com/api/:version/contacts/113/notes","organization":"https://:account.api-us1.com/api/:version/contacts/113/organization","plusAppend":"https://:account.api-us1.com/api/:version/contacts/113/plusAppend","trackingLogs":"https://:account.api-us1.com/api/:version/contacts/113/trackingLogs","scoreValues":"https://:account.api-us1.com/api/:version/contacts/113/scoreValues"},"id":"113","organization":""}}';

    public function let(
        ClientInterface $httpClient,
        SerializerInterface $deserializer,
        SerializerInterface $serializer,
        ContactInterface $contact,
        ResponseInterface $response
    ): void {
        $this->beConstructedWith($httpClient, $serializer, $deserializer);

        $serializer->serialize(['contact' => $contact], 'json')->willReturn(self::CREATE_CONTACT_REQUEST_PAYLOAD);

        $httpClient->send(Argument::type(Request::class))->willReturn($response);
    }

    public function it_implements_interface(): void
    {
        $this->shouldImplement(ActiveCampaignClientInterface::class);
    }

    public function it_creates_a_contact_on_active_campaign(
        SerializerInterface $deserializer,
        ResponseInterface $response,
        StreamInterface $responseBody,
        ContactInterface $contact
    ): void {
        $response->getStatusCode()->willReturn(201);
        $response->getBody()->willReturn($responseBody);
        $responseBody->getContents()->willReturn(self::CREATE_CONTACT_RESPONSE_PAYLOAD);
        $createContactResponse = new CreateContactResponse([], new ContactResponse('johndoe@example.com', '2018-09-28T13:50:41-05:00', '2018-09-28T13:50:41-05:00', '', [], 113, ''));
        $deserializer->deserialize(self::CREATE_CONTACT_RESPONSE_PAYLOAD, CreateContactResponse::class, 'json')->shouldBeCalledOnce()->willReturn($createContactResponse);

        $this->createContact($contact)->shouldReturn($createContactResponse);
    }

    public function it_throws_while_creating_a_contact_when_the_response_is_not_found(
        ResponseInterface $response,
        ContactInterface $contact,
        StreamInterface $stream
    ): void {
        $response->getStatusCode()->willReturn(404);
        $response->getBody()->willReturn($stream);
        $stream->getContents()->willReturn('{"message":"No Result found for Subscriber with id 1"}');

        $this->shouldThrow(new NotFoundHttpException('No Result found for Subscriber with id 1'))->during('createContact', [$contact]);
    }

    public function it_throws_while_creating_a_contact_when_the_response_is_not_processable(
        ResponseInterface $response,
        ContactInterface $contact,
        StreamInterface $stream
    ): void {
        $response->getStatusCode()->willReturn(422);
        $response->getBody()->willReturn($stream);
        $stream->getContents()->willReturn('{"errors":[{"title":"Email address already exists in the system","detail":"","code":"duplicate","source":{"pointer":"/data/attributes/email"}}]}');

        $this->shouldThrow(new UnprocessableEntityHttpException('Email address already exists in the system'))->during('createContact', [$contact]);
    }

    public function it_throws_while_creating_a_contact_when_the_response_is_not_recognized(
        ResponseInterface $response,
        ContactInterface $contact
    ): void {
        $response->getStatusCode()->willReturn(500);
        $response->getHeaders()->willReturn([]);
        $response->getReasonPhrase()->willReturn('Internal Server Error');

        $this->shouldThrow(new HttpException(500, 'Internal Server Error'))->during('createContact', [$contact]);
    }
}
