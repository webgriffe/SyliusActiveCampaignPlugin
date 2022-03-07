<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Client;

use GuzzleHttp\ClientInterface;
use Http\Message\MessageFactory;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignContactInterface;

final class ActiveCampaignClient implements ActiveCampaignClientInterface
{
    private ClientInterface $httpClient;

    private MessageFactory $requestFactory;

    private string $apiVersionedUrl;

    private string $apiKey;

    public function __construct(ClientInterface $httpClient, MessageFactory $requestFactory, string $apiUrl, string $apiKey)
    {
        $this->httpClient = $httpClient;
        $this->requestFactory = $requestFactory;
        $this->apiVersionedUrl = rtrim($apiUrl, '/') . '/api/3';
        $this->apiKey = $apiKey;
    }

    public function createContact(ActiveCampaignContactInterface $contact): void
    {
        $request = $this->requestFactory->createRequest(
            'POST',
            $this->apiVersionedUrl . '/contacts',
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Api-Token' => $this->apiKey,
                ],
                'body' => 'serialized contact', // todo: serialize content
            ]
        );
        $this->httpClient->send($request);
        // todo: check status code
        // todo: catch errors
        // todo: we should return a model of the created contact
    }
}
