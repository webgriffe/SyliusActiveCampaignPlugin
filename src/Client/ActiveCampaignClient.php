<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Client;

use GuzzleHttp\ClientInterface;
use Http\Message\MessageFactory;
use Symfony\Component\Serializer\SerializerInterface;
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
        private SerializerInterface $serializer,
        string $apiBaseUrl,
        private string $apiKey
    ) {
        $this->apiVersionedUrl = rtrim($apiBaseUrl, '/') . '/api/3';
    }

    public function createContact(ContactInterface $contact): CreateContactResponseInterface
    {
        $serializedContact = $this->serializer->serialize($contact, 'json');
        $request = $this->requestFactory->createRequest(
            'POST',
            $this->apiVersionedUrl . '/contacts',
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Api-Token' => $this->apiKey,
                ],
                'body' => $serializedContact,
            ]
        );

        $response = $this->httpClient->send($request);
        // todo: check status code
        // todo: catch errors

        $payload = $response->getBody()->getContents();

        $payloadArray = $this->serializer->deserialize($payload, 'array', 'json');
        $createContactResponse = $this->createContactResponseFactory->createNewFromPayload($payloadArray);

        return $createContactResponse;
    }
}
