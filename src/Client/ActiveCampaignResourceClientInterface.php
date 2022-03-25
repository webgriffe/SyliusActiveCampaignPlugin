<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Client;

use GuzzleHttp\Exception\GuzzleException;
use JsonException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ResourceInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\CreateResourceResponseInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ListResourcesResponseInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\UpdateResourceResponseInterface;

interface ActiveCampaignResourceClientInterface
{
    /**
     * @throws GuzzleException
     * @throws JsonException
     * @throws HttpException
     * @throws UnprocessableEntityHttpException
     * @throws NotFoundHttpException
     */
    public function create(ResourceInterface $resource): CreateResourceResponseInterface;

    /**
     * @param array<string, string> $queryParams
     */
    public function list(array $queryParams = []): ListResourcesResponseInterface;

    /**
     * @throws GuzzleException
     * @throws JsonException
     * @throws HttpException
     * @throws NotFoundHttpException
     */
    public function update(int $activeCampaignResourceId, ResourceInterface $resource): UpdateResourceResponseInterface;

    /**
     * @throws GuzzleException
     * @throws JsonException
     * @throws HttpException
     * @throws NotFoundHttpException
     */
    public function remove(int $activeCampaignResourceId): void;
}
