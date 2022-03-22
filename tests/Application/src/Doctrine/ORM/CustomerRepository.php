<?php

declare(strict_types=1);

namespace App\Doctrine\ORM;

use Sylius\Bundle\CoreBundle\Doctrine\ORM\CustomerRepository as BaseCustomerRepository;
use Webgriffe\SyliusActiveCampaignPlugin\Doctrine\ORM\ActiveCampaignResourceRepositoryTrait;
use Webgriffe\SyliusActiveCampaignPlugin\Repository\ActiveCampaignAwareRepositoryInterface;

final class CustomerRepository extends BaseCustomerRepository implements ActiveCampaignAwareRepositoryInterface
{
    use ActiveCampaignResourceRepositoryTrait;
}
