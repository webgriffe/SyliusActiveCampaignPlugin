<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign;

interface UpdateContactResponseInterface
{
    /** @return FieldValueInterface[] */
    public function getFieldValues(): array;

    public function getCreatedAt(): string;

    public function getEmail(): string;

    public function getPhone(): string;

    public function getFirstName(): string;

    public function getLastName(): string;

    public function getOrganizationId(): string;

    public function getSegmentioId(): string;

    public function getBouncedHard(): string;

    public function getBouncedSoft(): string;

    public function getBouncedDate(): ?string;

    public function getIp(): string;

    public function getUa(): ?string;

    public function getHash(): string;

    public function getSocialDataLastCheck(): ?string;

    public function getEmailLocal(): string;

    public function getEmailDomain(): string;

    public function getSentCnt(): string;

    public function getRatingTimestamp(): ?string;

    public function getGravatar(): string;

    public function getDeleted(): string;

    public function getAnonymized(): string;

    public function getADate(): ?string;

    public function getUpdatedAt(): string;

    public function getEDate(): ?string;

    public function getDeletedAt(): ?string;

    public function getCreatedAtUTCTimestamp(): string;

    public function getUpdatedAtUTCTimestamp(): string;

    /** @return array<string, string> */
    public function getLinks(): array;

    public function getId(): int;

    public function getOrganization(): ?string;
}
