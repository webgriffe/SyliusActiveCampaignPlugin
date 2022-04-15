<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Mapper;

use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ContactListInterface;

interface ContactListMapperInterface
{
    public function mapFromListContactStatusAndSourceId(int $listId, int $contactId, int $status, ?int $sourceId = null): ContactListInterface;
}
