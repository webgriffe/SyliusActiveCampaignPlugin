<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Client;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Request;
use RuntimeException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
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
            switch ($statusCode) {
                case 404:
                    /** @var array{message: string} $errorResponse */
                    $errorResponse = json_decode($response->getBody()->getContents(), true, 512, \JSON_THROW_ON_ERROR);

                    throw new NotFoundHttpException($errorResponse['message']);
                case 422:
                    /** @var array{errors: array{title: string, detail: string, code: string, source: array{pointer: string}}} $errorResponse */
                    $errorResponse = json_decode($response->getBody()->getContents(), true, 512, \JSON_THROW_ON_ERROR);
                    $titles = array_column($errorResponse['errors'], 'title');

                    throw new UnprocessableEntityHttpException(implode('; ', $titles));
                default:
                    throw new HttpException($statusCode, $response->getReasonPhrase(), null, $response->getHeaders());
            }
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
        $serializedContact = $this->serializer->serialize(
            ['contact' => $contact],
            'json'
        );

        $response = $this->httpClient->send(new Request(
            'PUT',
            self::API_ENDPOINT_VERSIONED . '/contacts/' . $activeCampaignContactId,
            [],
            $serializedContact
        ));
        if (($statusCode = $response->getStatusCode()) !== 200) {
            if ($statusCode === 404) {
                /** @var array{message: string} $errorResponse */
                $errorResponse = json_decode($response->getBody()->getContents(), true, 512, \JSON_THROW_ON_ERROR);

                throw new NotFoundHttpException($errorResponse['message']);
            }

            throw new HttpException($statusCode, $response->getReasonPhrase(), null, $response->getHeaders());
        }

        /** @var UpdateContactResponse $updateContactResponse */
        $updateContactResponse = $this->deserializer->deserialize(
            $response->getBody()->getContents(),
            UpdateContactResponse::class,
            'json'
        );

        return $updateContactResponse;
    }

    public function removeContact(int $activeCampaignContactId): void
    {
        throw new RuntimeException('TODO');
    }
}
