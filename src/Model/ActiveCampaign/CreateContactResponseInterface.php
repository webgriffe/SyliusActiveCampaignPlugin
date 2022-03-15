<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign;

interface CreateContactResponseInterface
{
    /** @return FieldValueInterface[] */
    public function getFieldValues(): array;

    public function getContact(): ContactResponseInterface;
}
