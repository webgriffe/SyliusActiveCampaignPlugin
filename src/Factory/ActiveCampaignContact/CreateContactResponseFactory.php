<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaignContact;

use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\CreateContactResponse;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\CreateContactResponseInterface;

final class CreateContactResponseFactory implements CreateContactResponseFactoryInterface
{
    public function createNewFromPayload(array $payload): CreateContactResponseInterface
    {
        $fieldValues = $payload['fieldValues'];
        $email = $payload['email'];
        $createdAt = $payload['cdate'];
        $updatedAt = $payload['udate'];
        $organizationId = $payload['origid'];
        $organization = $payload['organization'];
        $links = $payload['links'];
        $id = $payload['id'];

        return new CreateContactResponse($fieldValues, $email, $createdAt, $updatedAt, $organizationId, $links, $id, $organization);
    }
}
