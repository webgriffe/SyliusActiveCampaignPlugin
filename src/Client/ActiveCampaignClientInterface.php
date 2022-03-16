<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Client;

use GuzzleHttp\Exception\GuzzleException;
use JsonException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ContactInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\CreateContactResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\UpdateContactResponse;

interface ActiveCampaignClientInterface
{
    /**
     * @throws GuzzleException
     * @throws JsonException
     * @throws HttpException
     * @throws UnprocessableEntityHttpException
     * @throws NotFoundHttpException
     */
    public function createContact(ContactInterface $contact): CreateContactResponse;

    /**
     * @throws GuzzleException
     * @throws JsonException
     * @throws HttpException
     * @throws NotFoundHttpException
     */
    public function updateContact(int $activeCampaignContactId, ContactInterface $contact): UpdateContactResponse;

    /**
     * @throws GuzzleException
     * @throws JsonException
     * @throws HttpException
     * @throws NotFoundHttpException
     */
    public function removeContact(int $activeCampaignContactId): void;
}
