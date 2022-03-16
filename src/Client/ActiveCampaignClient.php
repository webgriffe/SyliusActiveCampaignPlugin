<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Client;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Request;
use RuntimeException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Serializer\SerializerInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ContactInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\CreateContactResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\UpdateContactResponse;

final class ActiveCampaignClient implements ActiveCampaignClientInterface
{
    private const API_ENDPOINT_VERSIONED = '/api/3';

    public function __construct(
        private ClientInterface $httpClient,
        private SerializerInterface $serializer,
        private SerializerInterface $deserializer
    ) {
    }

    public function createContact(ContactInterface $contact): CreateContactResponse
    {
        $serializedContact = $this->serializer->serialize(
            ['contact' => $contact],
            'json'
        );

        $response = $this->httpClient->send(new Request(
            'POST',
            self::API_ENDPOINT_VERSIONED . '/contacts',
            [],
            $serializedContact
        ));
        if (($statusCode = $response->getStatusCode()) !== 201) {
            throw new HttpException($statusCode);
        }

        /** @var CreateContactResponse $createContactResponse */
        $createContactResponse = $this->deserializer->deserialize(
            $response->getBody()->getContents(),
            CreateContactResponse::class,
            'json'
        );

        return $createContactResponse;
    }

    public function updateContact(int $activeCampaignContactId, ContactInterface $contact): UpdateContactResponse
    {
        throw new RuntimeException('TODO');
    }

    public function removeContact(int $activeCampaignContactId): void
    {
        throw new RuntimeException('TODO');
    }
}
