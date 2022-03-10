<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Client;

use GuzzleHttp\ClientInterface;
use Http\Message\MessageFactory;
use Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaignContact\CreateContactResponseFactoryInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ContactInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\CreateContactResponseInterface;

final class ActiveCampaignClient implements ActiveCampaignClientInterface
{
    private string $apiVersionedUrl;

    public function __construct(
        private ClientInterface $httpClient,
        private MessageFactory $requestFactory,
        private CreateContactResponseFactoryInterface $createContactResponseFactory,
        string $apiBaseUrl,
        private string $apiKey
    ) {
        $this->apiVersionedUrl = rtrim($apiBaseUrl, '/') . '/api/3';
    }

    public function createContact(ContactInterface $contact): CreateContactResponseInterface
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

        $response = $this->httpClient->send($request);
        // todo: check status code
        // todo: catch errors

        $payload = $response->getBody()->getContents();
        $payloadArray = json_decode($payload, true);
        $createContactResponse = $this->createContactResponseFactory->createNewFromPayload($payloadArray);

        return $createContactResponse;
    }
}
