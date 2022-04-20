<?php

declare(strict_types=1);

namespace App\Doctrine\ORM;

use Sylius\Bundle\CoreBundle\Doctrine\ORM\CustomerRepository as BaseCustomerRepository;
use Webgriffe\SyliusActiveCampaignPlugin\Doctrine\ORM\ActiveCampaignCustomerRepositoryTrait;
use Webgriffe\SyliusActiveCampaignPlugin\Repository\ActiveCampaignCustomerRepositoryInterface;

final class CustomerRepository extends BaseCustomerRepository implements ActiveCampaignCustomerRepositoryInterface
{
    use ActiveCampaignCustomerRepositoryTrait;
}
