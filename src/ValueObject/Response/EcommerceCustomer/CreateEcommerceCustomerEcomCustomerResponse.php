<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\EcommerceCustomer;

final class CreateEcommerceCustomerEcomCustomerResponse
{
    /** @param array<string, string> $links */
    public function __construct(
        private string $connectionId,
        private string $externalId,
        private string $email,
        private array $links,
        private int $id,
        private string $connection,
    ) {
    }

    public function getConnectionId(): string
    {
        return $this->connectionId;
    }

    public function getExternalId(): string
    {
        return $this->externalId;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string[]
     */
    public function getLinks(): array
    {
        return $this->links;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getConnection(): string
    {
        return $this->connection;
    }
}
