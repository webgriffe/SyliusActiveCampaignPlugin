<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusActiveCampaignPlugin\Client;

use GuzzleHttp\ClientInterface;
use Http\Message\MessageFactory;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use RuntimeException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Serializer\SerializerInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ContactInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\CreateContactResponseInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\CreateContactResponse;

final class ActiveCampaignClientSpec extends ObjectBehavior
{
    private const API_KEY = 'apitoken123456';

    private const API_URL = 'https://api-base-url.com';

    private const API_VERSIONED_URL = self::API_URL . '/api/3';

    private const CREATE_CONTACT_REQUEST_PAYLOAD = '{"contact":{"email":"test@email.com","firstName":"John","lastName":"Wayne","phone":"0123456789","fieldValues":[]}}';

    private const CREATE_CONTACT_RESPONSE_PAYLOAD = '{"fieldValues":[],"email":"test@email.com","cdate":"2022-03-07T10:16:24-06:00","udate":"2022-03-07T10:16:24-06:00","origid":"ABC123","organization":"Webgriffe SRL","links":[],"id":"1"}';

    public function let(
        ClientInterface $httpClient,
        MessageFactory $requestFactory,
        SerializerInterface $serializer,
        ContactInterface $contact,
        RequestInterface $request,
        ResponseInterface $response,
        StreamInterface $responseBody,
    ): void {
        $this->beConstructedWith($httpClient, $requestFactory, $serializer, self::API_URL, self::API_KEY);

        $requestFactory
            ->createRequest(Argument::any(), Argument::any(), Argument::any())
            ->willReturn($request);

        $serializer->serialize($contact, 'json')->willReturn(self::CREATE_CONTACT_REQUEST_PAYLOAD);

        $httpClient->send($request)->willReturn($response);
        $response->getBody()->willReturn($responseBody);
    }

    public function it_implements_interface(): void
    {
        $this->shouldImplement(ActiveCampaignClientInterface::class);
    }

    public function it_creates_a_contact_on_active_campaign(
        ClientInterface $httpClient,
        MessageFactory $requestFactory,
        SerializerInterface $serializer,
        RequestInterface $request,
        ResponseInterface $response,
        StreamInterface $responseBody,
        ContactInterface $contact,
        CreateContactResponseInterface $createContactResponse,
    ): void {
        $response->getStatusCode()->willReturn(201);
        $responseBody->getContents()->willReturn(self::CREATE_CONTACT_RESPONSE_PAYLOAD);
        $serializer->deserialize(self::CREATE_CONTACT_RESPONSE_PAYLOAD, CreateContactResponse::class, 'json')->shouldBeCalledOnce()->willReturn($createContactResponse);

        $requestFactory
            ->createRequest(
                'POST',
                self::API_VERSIONED_URL . '/contacts',
                [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Api-Token' => self::API_KEY,
                    ],
                    'body' => self::CREATE_CONTACT_REQUEST_PAYLOAD,
                ]
            )
            ->shouldBeCalledOnce()
            ->willReturn($request);
        $httpClient->send($request)->shouldBeCalledOnce()->willReturn($response);

        $this->createContact($contact)->shouldReturn($createContactResponse);
    }

    public function it_throws_while_creating_a_contact_and_the_request_wasnt_successful(
        ResponseInterface $response,
        ContactInterface $contact,
    ): void
    {
        $response->getStatusCode()->willReturn(500);

        $this->shouldThrow(HttpException::class)->during('createContact', [$contact]);
    }

    public function it_throws_while_creating_a_contact_and_the_response_wasnt_deserialized_properly(
        SerializerInterface $serializer,
        ResponseInterface $response,
        ContactInterface $contact,
        StreamInterface $responseBody,
    ): void
    {
        $response->getStatusCode()->willReturn(200);
        $responseBody->getContents()->willReturn(self::CREATE_CONTACT_RESPONSE_PAYLOAD);
        $serializer->deserialize(self::CREATE_CONTACT_RESPONSE_PAYLOAD, 'array', 'json')->willReturn('not an array');

        $this->shouldThrow(RuntimeException::class)->during('createContact', [$contact]);
    }
}
