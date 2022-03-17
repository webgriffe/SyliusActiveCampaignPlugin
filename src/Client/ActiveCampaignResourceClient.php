<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Client;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Serializer\SerializerInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ResourceInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\CreateResourceResponseInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\UpdateResourceResponseInterface;

final class ActiveCampaignResourceClient implements ActiveCampaignResourceClientInterface
{
    private const API_ENDPOINT_VERSIONED = '/api/3';

    public function __construct(
        private ClientInterface $httpClient,
        private SerializerInterface $serializer,
        private SerializerInterface $deserializer,
        private string $resourceName,
        private string $createResourceResponseType,
        private string $updateResourceResponseType
    ) {
    }

    public function create(ResourceInterface $resource): CreateResourceResponseInterface
    {
        $serializedResource = $this->serializer->serialize(
            [$this->resourceName => $resource],
            'json'
        );

        $response = $this->httpClient->send(new Request(
            'POST',
            self::API_ENDPOINT_VERSIONED . '/' . $this->resourceName . 's',
            [],
            $serializedResource
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

        /** @var CreateResourceResponseInterface $createResourceResponse */
        $createResourceResponse = $this->deserializer->deserialize(
            $response->getBody()->getContents(),
            $this->createResourceResponseType,
            'json',
            ['resource' => $this->resourceName]
        );

        return $createResourceResponse;
    }

    public function update(int $activeCampaignResourceId, ResourceInterface $resource): UpdateResourceResponseInterface
    {
        $serializedResource = $this->serializer->serialize(
            [$this->resourceName => $resource],
            'json'
        );

        $response = $this->httpClient->send(new Request(
            'PUT',
            self::API_ENDPOINT_VERSIONED . '/' . $this->resourceName . 's' . '/' . $activeCampaignResourceId,
            [],
            $serializedResource
        ));
        if (($statusCode = $response->getStatusCode()) !== 200) {
            if ($statusCode === 404) {
                /** @var array{message: string} $errorResponse */
                $errorResponse = json_decode($response->getBody()->getContents(), true, 512, \JSON_THROW_ON_ERROR);

                throw new NotFoundHttpException($errorResponse['message']);
            }

            throw new HttpException($statusCode, $response->getReasonPhrase(), null, $response->getHeaders());
        }

        /** @var UpdateResourceResponseInterface $updateResourceResponse */
        $updateResourceResponse = $this->deserializer->deserialize(
            $response->getBody()->getContents(),
            $this->updateResourceResponseType,
            'json',
            ['resource' => $this->resourceName]
        );

        return $updateResourceResponse;
    }

    public function remove(int $activeCampaignResourceId): void
    {
        $response = $this->httpClient->send(new Request(
            'DELETE',
            self::API_ENDPOINT_VERSIONED . '/' . $this->resourceName . 's' . '/' . $activeCampaignResourceId
        ));
        if (($statusCode = $response->getStatusCode()) === 200) {
            return;
        }
        if ($statusCode === 404) {
            /** @var array{message: string} $errorResponse */
            $errorResponse = json_decode($response->getBody()->getContents(), true, 512, \JSON_THROW_ON_ERROR);

            throw new NotFoundHttpException($errorResponse['message']);
        }

        throw new HttpException($statusCode, $response->getReasonPhrase(), null, $response->getHeaders());
    }
}
