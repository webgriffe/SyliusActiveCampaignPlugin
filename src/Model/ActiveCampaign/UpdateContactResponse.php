<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign;

final class UpdateContactResponse implements UpdateContactResponseInterface
{
    /**
     * @param FieldValueInterface[] $fieldValues
     * @param array<string, string> $links
     */
    public function __construct(
        private array $fieldValues,
        private string $createdAt,
        private string $email,
        private string $phone,
        private string $firstName,
        private string $lastName,
        private string $organizationId,
        private string $segmentioId,
        private string $bouncedHard,
        private string $bouncedSoft,
        private string $ip,
        private string $hash,
        private string $emailLocal,
        private string $emailDomain,
        private string $sentCnt,
        private string $gravatar,
        private string $deleted,
        private string $anonymized,
        private string $updatedAt,
        private string $createdAtUTCTimestamp,
        private string $updatedAtUTCTimestamp,
        private array $links,
        private int $id,
        private ?string $bouncedDate = null,
        private ?string $ua = null,
        private ?string $socialDataLastCheck = null,
        private ?string $ratingTimestamp = null,
        private ?string $aDate = null,
        private ?string $eDate = null,
        private ?string $deletedAt = null,
        private ?string $organization = null
    ) {
    }

    public function getFieldValues(): array
    {
        return $this->fieldValues;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getOrganizationId(): string
    {
        return $this->organizationId;
    }

    public function getSegmentioId(): string
    {
        return $this->segmentioId;
    }

    public function getBouncedHard(): string
    {
        return $this->bouncedHard;
    }

    public function getBouncedSoft(): string
    {
        return $this->bouncedSoft;
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function getEmailLocal(): string
    {
        return $this->emailLocal;
    }

    public function getEmailDomain(): string
    {
        return $this->emailDomain;
    }

    public function getSentCnt(): string
    {
        return $this->sentCnt;
    }

    public function getGravatar(): string
    {
        return $this->gravatar;
    }

    public function getDeleted(): string
    {
        return $this->deleted;
    }

    public function getAnonymized(): string
    {
        return $this->anonymized;
    }

    public function getUpdatedAt(): string
    {
        return $this->updatedAt;
    }

    public function getCreatedAtUTCTimestamp(): string
    {
        return $this->createdAtUTCTimestamp;
    }

    public function getUpdatedAtUTCTimestamp(): string
    {
        return $this->updatedAtUTCTimestamp;
    }

    public function getLinks(): array
    {
        return $this->links;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getBouncedDate(): ?string
    {
        return $this->bouncedDate;
    }

    public function getUa(): ?string
    {
        return $this->ua;
    }

    public function getSocialDataLastCheck(): ?string
    {
        return $this->socialDataLastCheck;
    }

    public function getRatingTimestamp(): ?string
    {
        return $this->ratingTimestamp;
    }

    public function getADate(): ?string
    {
        return $this->aDate;
    }

    public function getEDate(): ?string
    {
        return $this->eDate;
    }

    public function getDeletedAt(): ?string
    {
        return $this->deletedAt;
    }

    public function getOrganization(): ?string
    {
        return $this->organization;
    }
}
