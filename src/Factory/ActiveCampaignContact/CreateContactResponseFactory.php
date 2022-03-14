<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaignContact;

use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\CreateContactResponse;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\CreateContactResponseInterface;
use Webmozart\Assert\Assert;

final class CreateContactResponseFactory implements CreateContactResponseFactoryInterface
{
    public function createNewFromPayload(array $payload): CreateContactResponseInterface
    {
        // is there any better way to do this?
        Assert::isArray($payload['fieldValues']);
        $fieldValues = $payload['fieldValues'];
        Assert::string($payload['email']);
        $email = $payload['email'];
        Assert::string($payload['cdate']);
        $createdAt = $payload['cdate'];
        Assert::string($payload['udate']);
        $updatedAt = $payload['udate'];
        Assert::string($payload['origid']);
        $organizationId = $payload['origid'];
        Assert::string($payload['organization']);
        $organization = $payload['organization'];
        Assert::isArray($payload['links']);
        $links = $payload['links'];
        $id = (int) $payload['id'];

        /** @psalm-suppress MixedArgumentTypeCoercion */
        return new CreateContactResponse($fieldValues, $email, $createdAt, $updatedAt, $organizationId, $links, $id, $organization);
    }
}
