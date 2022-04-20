<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Repository;

use Sylius\Component\Core\Model\CustomerInterface;

/** @extends ActiveCampaignResourceRepositoryInterface<CustomerInterface> */
interface ActiveCampaignCustomerRepositoryInterface extends ActiveCampaignResourceRepositoryInterface
{
    public function findByContactId(int $contactId): ?CustomerInterface;
}
