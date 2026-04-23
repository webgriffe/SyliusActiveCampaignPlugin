<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Client;

use DateTimeInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Request;
use InvalidArgumentException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ResourceInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\CreateResourceResponseInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ListResourcesResponseInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\RetrieveResourceResponseInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\UpdateResourceResponseInterface;

/**
 * Don't know why, but Psalm says that on PHP 8.1 and 8.2 this constant is undefined! Anyway, if Psalm says the truth,
 * the following condition should fix the case where that constant doesn't exist.
 *
 * @psalm-suppress UndefinedConstant
 * @psalm-suppress MixedArgument
 */
if (class_exists(AbstractObjectNormalizer::class) && defined(AbstractObjectNormalizer::DISABLE_TYPE_ENFORCEMENT)) {
    define("Webgriffe\SyliusActiveCampaignPlugin\Client\DISABLE_TYPE_ENFORCEMENT", AbstractObjectNormalizer::DISABLE_TYPE_ENFORCEMENT);
} else {
    define("Webgriffe\SyliusActiveCampaignPlugin\Client\DISABLE_TYPE_ENFORCEMENT", 'disable_type_enforcement');
}

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
        private ?string $updateResourceResponseType = null,
    ) {
    }

    #[\Override]
    public function create(ResourceInterface $resource): CreateResourceResponseInterface
    {
        if ($this->createResourceResponseType === null) {
            throw new InvalidArgumentException('You should pass the CreateResourceResponse argument to the resource client');
        }
        $serializedResource = $this->serializer->serialize(
            [$this->resourceName => $resource],
            'json',
            [
                DateTimeNormalizer::FORMAT_KEY => DateTimeInterface::ATOM,
            ],
        );

        $response = $this->httpClient->send(new Request(
            'POST',
            self::API_ENDPOINT_VERSIONED . '/' . $this->resourceName . 's',
            [],
            $serializedResource,
        ));
        if (($statusCode = $response->getStatusCode()) !== 201) {
            switch ($statusCode) {
                case 404:
                    /** @var array{message: string} $errorResponse */
                    $errorResponse = json_decode($response->getBody()->getContents(), true, 512, \JSON_THROW_ON_ERROR);

                    throw new NotFoundHttpException(sprintf('[%s] %s | payload: %s', $this->resourceName, $errorResponse['message'], $serializedResource));
                case 422:
                    /** @var array{errors?: array{title: string, detail: string, code: string, source: array{pointer: string}}|null} $errorResponse */
                    $errorResponse = json_decode($response->getBody()->getContents(), true, 512, \JSON_THROW_ON_ERROR);
                    if (array_key_exists('errors', $errorResponse) && is_array($errorResponse['errors'])) {
                        /** @var string[] $titles */
                        $titles = array_column($errorResponse['errors'], 'title');
                        $errorMessage = implode('; ', $titles);
                    } else {
                        $errorMessage = 'Errors key does not exists on response "' . json_encode($errorResponse, \JSON_THROW_ON_ERROR) . '"';
                    }

                    throw new UnprocessableEntityHttpException(sprintf('[%s] %s | payload: %s', $this->resourceName, $errorMessage, $serializedResource));
                default:
                    throw new HttpException($statusCode, $response->getReasonPhrase(), null, $response->getHeaders());
            }
        }

        /** @var CreateResourceResponseInterface $createResourceResponse */
        $createResourceResponse = $this->serializer->deserialize(
            $response->getBody()->getContents(),
            $this->createResourceResponseType,
            'json',
            [
                'resource' => $this->resourceName,
                DateTimeNormalizer::FORMAT_KEY => DateTimeInterface::ATOM,
                DISABLE_TYPE_ENFORCEMENT => true,
            ],
        );

        return $createResourceResponse;
    }

    #[\Override]
    public function get(int $resourceId): RetrieveResourceResponseInterface
    {
        if ($this->retrieveResourceResponseType === null) {
            throw new InvalidArgumentException('You should pass the RetrieveResourceResponse argument to the resource client');
        }
        $response = $this->httpClient->send(new Request(
            'GET',
            self::API_ENDPOINT_VERSIONED . '/' . $this->resourceName . 's' . '/' . $resourceId,
        ));
        if (($statusCode = $response->getStatusCode()) !== 200) {
            if ($statusCode === 404) {
                /** @var array{message: string} $errorResponse */
                $errorResponse = json_decode($response->getBody()->getContents(), true, 512, \JSON_THROW_ON_ERROR);

                throw new NotFoundHttpException(sprintf('[%s #%d] %s', $this->resourceName, $resourceId, $errorResponse['message']));
            }

            throw new HttpException($statusCode, $response->getReasonPhrase(), null, $response->getHeaders());
        }

        /** @var RetrieveResourceResponseInterface $retrieveResourceResponse */
        $retrieveResourceResponse = $this->serializer->deserialize(
            $response->getBody()->getContents(),
            $this->retrieveResourceResponseType,
            'json',
            [
                'resource' => $this->resourceName,
                DateTimeNormalizer::FORMAT_KEY => DateTimeInterface::ATOM,
                DISABLE_TYPE_ENFORCEMENT => true,
            ],
        );

        return $retrieveResourceResponse;
    }

    #[\Override]
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
                DateTimeNormalizer::FORMAT_KEY => DateTimeInterface::ATOM,
                DISABLE_TYPE_ENFORCEMENT => true,
            ],
        );

        return $listResourcesResponse;
    }

    #[\Override]
    public function update(int $activeCampaignResourceId, ResourceInterface $resource): UpdateResourceResponseInterface
    {
        if ($this->updateResourceResponseType === null) {
            throw new InvalidArgumentException('You should pass the UpdateResourceResponse argument to the resource client');
        }
        $serializedResource = $this->serializer->serialize(
            [$this->resourceName => $resource],
            'json',
            [DateTimeNormalizer::FORMAT_KEY => DateTimeInterface::ATOM],
        );

        $response = $this->httpClient->send(new Request(
            'PUT',
            self::API_ENDPOINT_VERSIONED . '/' . $this->resourceName . 's' . '/' . $activeCampaignResourceId,
            [],
            $serializedResource,
        ));
        if (($statusCode = $response->getStatusCode()) !== 200) {
            switch ($statusCode) {
                case 404:
                    /** @var array{message: string} $errorResponse */
                    $errorResponse = json_decode($response->getBody()->getContents(), true, 512, \JSON_THROW_ON_ERROR);

                    throw new NotFoundHttpException(sprintf('[%s #%d] %s | payload: %s', $this->resourceName, $activeCampaignResourceId, $errorResponse['message'], $serializedResource));
                case 422:
                    /** @var array{errors?: array{title: string, detail: string, code: string, source: array{pointer: string}}|null} $errorResponse */
                    $errorResponse = json_decode($response->getBody()->getContents(), true, 512, \JSON_THROW_ON_ERROR);
                    if (array_key_exists('errors', $errorResponse) && is_array($errorResponse['errors'])) {
                        /** @var string[] $titles */
                        $titles = array_column($errorResponse['errors'], 'title');
                        $errorMessage = implode('; ', $titles);
                    } else {
                        $errorMessage = 'Errors key does not exists on response "' . json_encode($errorResponse, \JSON_THROW_ON_ERROR) . '"';
                    }

                    throw new UnprocessableEntityHttpException(sprintf('[%s #%d] %s | payload: %s', $this->resourceName, $activeCampaignResourceId, $errorMessage, $serializedResource));
                default:
                    throw new HttpException($statusCode, $response->getReasonPhrase(), null, $response->getHeaders());
            }
        }

        /** @var UpdateResourceResponseInterface $updateResourceResponse */
        $updateResourceResponse = $this->serializer->deserialize(
            $response->getBody()->getContents(),
            $this->updateResourceResponseType,
            'json',
            [
                'resource' => $this->resourceName,
                DateTimeNormalizer::FORMAT_KEY => DateTimeInterface::ATOM,
                DISABLE_TYPE_ENFORCEMENT => true,
            ],
        );

        return $updateResourceResponse;
    }

    #[\Override]
    public function remove(int $activeCampaignResourceId): void
    {
        $response = $this->httpClient->send(new Request(
            'DELETE',
            self::API_ENDPOINT_VERSIONED . '/' . $this->resourceName . 's' . '/' . $activeCampaignResourceId,
        ));
        if (($statusCode = $response->getStatusCode()) === 200) {
            return;
        }
        if ($statusCode === 404) {
            /** @var array{message: string} $errorResponse */
            $errorResponse = json_decode($response->getBody()->getContents(), true, 512, \JSON_THROW_ON_ERROR);

            throw new NotFoundHttpException(sprintf('[%s #%d] %s', $this->resourceName, $activeCampaignResourceId, $errorResponse['message']));
        }

        throw new HttpException($statusCode, $response->getReasonPhrase(), null, $response->getHeaders());
    }
}
