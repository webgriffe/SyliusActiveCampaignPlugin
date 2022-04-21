<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Client;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Request;
use InvalidArgumentException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Serializer\SerializerInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ResourceInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\CreateResourceResponseInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ListResourcesResponseInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\RetrieveResourceResponseInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\UpdateResourceResponseInterface;

final class ActiveCampaignResourceClient implements ActiveCampaignResourceClientInterface
{
    private const API_ENDPOINT_VERSIONED = '/api/3';

    public function __construct(
        private ClientInterface $httpClient,
        private SerializerInterface $serializer,
        private string $resourceName,
        private ?string $resourceResponseType = null,
        private ?string $createResourceResponseType = null,
        private ?string $retrieveResourceResponseType = null,
        private ?string $listResourcesResponseType = null,
        private ?string $updateResourceResponseType = null
    ) {
    }

    public function create(ResourceInterface $resource): CreateResourceResponseInterface
    {
        if ($this->createResourceResponseType === null) {
            throw new InvalidArgumentException('You should pass the CreateResourceResponse argument to the resource client');
        }
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
        $createResourceResponse = $this->serializer->deserialize(
            $response->getBody()->getContents(),
            $this->createResourceResponseType,
            'json',
            ['resource' => $this->resourceName]
        );

        return $createResourceResponse;
    }

    public function get(int $resourceId): RetrieveResourceResponseInterface
    {
        if ($this->retrieveResourceResponseType === null) {
            throw new InvalidArgumentException('You should pass the RetrieveResourceResponse argument to the resource client');
        }
        $response = $this->httpClient->send(new Request(
            'GET',
            self::API_ENDPOINT_VERSIONED . '/' . $this->resourceName . 's' . '/' . $resourceId
        ));
        if (($statusCode = $response->getStatusCode()) !== 200) {
            if ($statusCode === 404) {
                /** @var array{message: string} $errorResponse */
                $errorResponse = json_decode($response->getBody()->getContents(), true, 512, \JSON_THROW_ON_ERROR);

                throw new NotFoundHttpException($errorResponse['message']);
            }

            throw new HttpException($statusCode, $response->getReasonPhrase(), null, $response->getHeaders());
        }

        /** @var RetrieveResourceResponseInterface $retrieveResourceResponse */
        $retrieveResourceResponse = $this->serializer->deserialize(
            $response->getBody()->getContents(),
            $this->retrieveResourceResponseType,
            'json',
            ['resource' => $this->resourceName]
        );

        return $retrieveResourceResponse;
    }

    public function list(array $queryParams = []): ListResourcesResponseInterface
    {
        if ($this->listResourcesResponseType === null) {
            throw new InvalidArgumentException('You should pass the ListResourceResponse argument to the resource client');
        }
        $httpBuildQuery = http_build_query($queryParams);
        $response = $this->httpClient->send(new Request(
            'GET',
            self::API_ENDPOINT_VERSIONED . '/' . $this->resourceName . 's' . ($httpBuildQuery !== '' ? '?' . $httpBuildQuery : ''),
        ));
        if (($statusCode = $response->getStatusCode()) !== 200) {
            if ($statusCode === 400) {
                throw new BadRequestHttpException($response->getReasonPhrase(), null, 0, $response->getHeaders());
            }

            throw new HttpException($statusCode, $response->getReasonPhrase(), null, $response->getHeaders());
        }

        /** @var ListResourcesResponseInterface $listResourcesResponse */
        $listResourcesResponse = $this->serializer->deserialize(
            $response->getBody()->getContents(),
            $this->listResourcesResponseType,
            'json',
            [
                'resource' => $this->resourceName,
                'responseType' => $this->resourceResponseType,
                'type' => ListResourcesResponseInterface::class,
            ]
        );

        return $listResourcesResponse;
    }

    public function update(int $activeCampaignResourceId, ResourceInterface $resource): UpdateResourceResponseInterface
    {
        if ($this->updateResourceResponseType === null) {
            throw new InvalidArgumentException('You should pass the UpdateResourceResponse argument to the resource client');
        }
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
        $updateResourceResponse = $this->serializer->deserialize(
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
