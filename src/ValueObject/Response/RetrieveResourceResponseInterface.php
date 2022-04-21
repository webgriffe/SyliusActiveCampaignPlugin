<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response;

interface RetrieveResourceResponseInterface
{
    public function getResourceResponse(): ResourceResponseInterface;
}
