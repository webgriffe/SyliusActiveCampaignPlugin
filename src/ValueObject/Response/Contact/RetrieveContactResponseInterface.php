<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Contact;

use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\RetrieveResourceResponseInterface;

interface RetrieveContactResponseInterface extends RetrieveResourceResponseInterface
{
    /** @return array<array-key, array{contact: string, list: string, status: string, id: string}> */
    public function getContactLists(): array;
}
