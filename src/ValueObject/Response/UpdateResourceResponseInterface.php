<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response;

/** @psalm-api */
interface UpdateResourceResponseInterface
{
    public function getResourceResponse(): ResourceResponseInterface;
}
