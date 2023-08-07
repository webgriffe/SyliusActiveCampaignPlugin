<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusActiveCampaignPlugin\App\Entity\Customer;

use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Core\Model\Customer as BaseCustomer;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareTrait;
use Webgriffe\SyliusActiveCampaignPlugin\Model\CustomerActiveCampaignAwareTrait;

/**
 * @ORM\Entity
 * @ORM\Table(name="sylius_customer")
 */
class Customer extends BaseCustomer implements CustomerInterface
{
    use ActiveCampaignAwareTrait;
    use CustomerActiveCampaignAwareTrait;
}
