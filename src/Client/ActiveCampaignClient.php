<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Client;

use GuzzleHttp\ClientInterface;
use Http\Message\MessageFactory;
use RuntimeException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Serializer\SerializerInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ContactInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\CreateContactResponse;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\CreateContactResponseInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\UpdateContactResponseInterface;

final class ActiveCampaignClient implements ActiveCampaignClientInterface
{
    private string $apiVersionedUrl;

    public function __construct(
        private ClientInterface $httpClient,
        private MessageFactory $requestFactory,
        private SerializerInterface $serializer,
        private SerializerInterface $deserializer,
        string $apiBaseUrl,
        private string $apiKey
    ) {
        $this->apiVersionedUrl = rtrim($apiBaseUrl, '/') . '/api/3';
    }

    public function createContact(ContactInterface $contact): CreateContactResponseInterface
    {
        $serializedContact = $this->serializer->serialize(['contact' => $contact], 'json');
        $request = $this->requestFactory->createRequest(
            'POST',
            '/api/3/contacts',
            [],
            $serializedContact
        );

        $response = $this->httpClient->send($request);
        if (($statusCode = $response->getStatusCode()) !== 201) {
            throw new HttpException($statusCode);
        }

        $body = $response->getBody();
        $payload = $body->getContents();

        /** @var CreateContactResponse $createContactResponse */
        $createContactResponse = $this->deserializer->deserialize($payload, CreateContactResponse::class, 'json');

        return $createContactResponse;
    }

    public function updateContact(int $activeCampaignContactId, ContactInterface $contact): UpdateContactResponseInterface
    {
        throw new RuntimeException('TODO');
    }

    public function removeContact(int $activeCampaignContactId): void
    {
        throw new RuntimeException('TODO');
    }
}
