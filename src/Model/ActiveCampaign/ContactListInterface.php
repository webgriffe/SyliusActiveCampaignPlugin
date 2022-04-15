<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign;

interface ContactListInterface extends ResourceInterface
{
    public function getListId(): int;

    public function setListId(int $listId): void;

    public function getContactId(): int;

    public function setContactId(int $contactId): void;

    public function getStatus(): int;

    public function setStatus(int $status): void;

    public function getSourceId(): ?int;

    public function setSourceId(?int $sourceId): void;
}
