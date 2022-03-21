<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response;

interface UpdateResourceResponseInterface
{
    public function getResourceResponse(): ResourceResponseInterface;
}
