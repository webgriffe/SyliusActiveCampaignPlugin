<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response;

interface ListResourcesResponseInterface
{
    /** @return ResourceResponseInterface[] */
    public function getResourceResponseLists(): array;
}
