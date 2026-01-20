<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ContactList;

use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ResourceResponseInterface;

/** @psalm-api */
final class ContactListResponse implements ResourceResponseInterface
{
    public function __construct(
        private int $id,
    ) {
    }

    #[\Override]
    public function getId(): int
    {
        return $this->id;
    }
}
