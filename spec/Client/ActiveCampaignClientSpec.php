<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusActiveCampaignPlugin\Client;

use GuzzleHttp\ClientInterface;
use Http\Message\MessageFactory;
use PhpSpec\ObjectBehavior;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ContactInterface;

final class ActiveCampaignClientSpec extends ObjectBehavior
{
    private const API_KEY = 'apitoken123456';

    private const API_URL = 'https://api-base-url.com';

    private const API_VERSIONED_URL = self::API_URL . '/api/3';

    public function let(ClientInterface $httpClient, MessageFactory $requestFactory): void
    {
        $this->beConstructedWith($httpClient, $requestFactory, self::API_URL, self::API_KEY);
    }

    public function it_implements_interface(): void
    {
        $this->shouldImplement(ActiveCampaignClientInterface::class);
    }

    public function it_creates_a_contact_on_active_campaign(
        ClientInterface $httpClient,
        MessageFactory $requestFactory,
        RequestInterface $request,
        ResponseInterface $response,
        ContactInterface $contact,
    ): void {
        $requestFactory
            ->createRequest(
                'POST',
                self::API_VERSIONED_URL . '/contacts',
                [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Api-Token' => self::API_KEY,
                    ],
                    'body' => 'serialized contact',
                ]
            )
            ->shouldBeCalledOnce()
            ->willReturn($request);

        $httpClient->send($request)->shouldBeCalledOnce()->willReturn($response);

        $this->createContact($contact);
    }
}
