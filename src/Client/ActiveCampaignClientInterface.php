<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Client;

use GuzzleHttp\Exception\GuzzleException;
use JsonException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ContactInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\CreateContactResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\UpdateContactResponse;

interface ActiveCampaignClientInterface
{
    /**
     * @throws GuzzleException
     * @throws JsonException
     * @throws HttpException
     */
    public function createContact(ContactInterface $contact): CreateContactResponse;

    public function updateContact(int $activeCampaignContactId, ContactInterface $contact): UpdateContactResponse;

    public function removeContact(int $activeCampaignContactId): void;
}
