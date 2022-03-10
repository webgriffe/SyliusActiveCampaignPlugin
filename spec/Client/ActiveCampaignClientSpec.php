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
use Symfony\Component\Serializer\SerializerInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaignContact\CreateContactResponseFactoryInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ContactInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\CreateContactResponseInterface;

final class ActiveCampaignClientSpec extends ObjectBehavior
{
    private const API_KEY = 'apitoken123456';

    private const API_URL = 'https://api-base-url.com';

    private const API_VERSIONED_URL = self::API_URL . '/api/3';

    public function let(
        ClientInterface $httpClient,
        MessageFactory $requestFactory,
        CreateContactResponseFactoryInterface $createContactResponseFactory,
        SerializerInterface $serializer,
    ): void {
        $this->beConstructedWith($httpClient, $requestFactory, $createContactResponseFactory, $serializer, self::API_URL, self::API_KEY);
    }

    public function it_implements_interface(): void
    {
        $this->shouldImplement(ActiveCampaignClientInterface::class);
    }

    public function it_creates_a_contact_on_active_campaign(
        ClientInterface $httpClient,
        MessageFactory $requestFactory,
        CreateContactResponseFactoryInterface $createContactResponseFactory,
        SerializerInterface $serializer,
        RequestInterface $request,
        ResponseInterface $response,
        StreamInterface $responseBody,
        ContactInterface $contact,
        CreateContactResponseInterface $contactResponse,
    ): void {
        $responsePayload = '{"fieldValues":[],"email":"test@email.com","cdate":"2022-03-07T10:16:24-06:00","udate":"2022-03-07T10:16:24-06:00","origid":"ABC123","organization":"Webgriffe SRL","links":[],"id":"1"}';
        $requestPayload = '{"contact":{"email":"test@email.com","firstName":"John","lastName":"Wayne","phone":"0123456789","fieldValues":[]}}';

        $response->getBody()->willReturn($responseBody);
        $responseBody->getContents()->willReturn($responsePayload);

        $serializer->serialize($contact, 'json')->willReturn($requestPayload);
        $serializer->deserialize($responsePayload, 'array', 'json')->willReturn(['email' => 'test@email.com']);

        $requestFactory
            ->createRequest(
                'POST',
                self::API_VERSIONED_URL . '/contacts',
                [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Api-Token' => self::API_KEY,
                    ],
                    'body' => $requestPayload,
                ]
            )
            ->shouldBeCalledOnce()
            ->willReturn($request);


        $httpClient->send($request)->shouldBeCalledOnce()->willReturn($response);
        $createContactResponseFactory->createNewFromPayload(Argument::any())->willReturn($contactResponse);

        $this->createContact($contact)->shouldReturn($contactResponse);
    }
}
