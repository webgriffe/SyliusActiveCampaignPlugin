<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Client;

use GuzzleHttp\ClientInterface;
use Http\Message\MessageFactory;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignContactInterface;

final class ActiveCampaignClient implements ActiveCampaignClientInterface
{
    private string $apiVersionedUrl;

    public function __construct(
        private ClientInterface $httpClient,
        private MessageFactory $requestFactory,
        string $apiBaseUrl,
        private string $apiKey
    ) {
        $this->apiVersionedUrl = rtrim($apiBaseUrl, '/') . '/api/3';
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
