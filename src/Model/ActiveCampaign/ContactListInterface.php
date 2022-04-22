<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign;

interface ContactListInterface extends ResourceInterface
{
    public const UNSUBSCRIBED_STATUS_CODE = 2;

    public const UNCONFIRMED_STATUS_CODE = 0;

    public const SUBSCRIBED_STATUS_CODE = 1;

    public const BOUNCED_STATUS_CODE = 3;

    public function getListId(): int;

    public function setListId(int $listId): void;

    public function getContactId(): int;

    public function setContactId(int $contactId): void;

    public function getStatus(): int;

    public function setStatus(int $status): void;

    public function getSourceId(): ?int;

    public function setSourceId(?int $sourceId): void;
}
