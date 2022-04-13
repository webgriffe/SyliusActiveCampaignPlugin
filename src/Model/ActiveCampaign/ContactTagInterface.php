<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign;

interface ContactTagInterface extends ResourceInterface
{
    public function getContactId(): int;

    public function setContactId(int $contactId): void;

    public function getTagId(): int;

    public function setTagId(int $tagId): void;
}
