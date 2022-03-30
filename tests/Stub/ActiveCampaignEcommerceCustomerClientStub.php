<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusActiveCampaignPlugin\Stub;

use RuntimeException;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignResourceClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ResourceInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\CreateResourceResponseInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\EcommerceCustomer\CreateEcommerceCustomerResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\EcommerceCustomer\EcommerceCustomerResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\EcommerceCustomer\ListEcommerceCustomersResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\EcommerceCustomer\UpdateEcommerceCustomerResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ListResourcesResponseInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\UpdateResourceResponseInterface;

final class ActiveCampaignEcommerceCustomerClientStub implements ActiveCampaignResourceClientInterface
{
    public static int $activeCampaignResourceId = 3423;

    /** @var array{email: string, connectionid: string, ecommerceCustomer: EcommerceCustomerResponse} */
    public static array $activeCampaignResources = [];

    public function create(ResourceInterface $resource): CreateResourceResponseInterface
    {
        return new CreateEcommerceCustomerResponse(
            new EcommerceCustomerResponse(
                self::$activeCampaignResourceId
            )
        );
    }

    public function list(array $queryParams = []): ListResourcesResponseInterface
    {
        $ecommerceCustomers = [];
        $emailToSearch = null;
        $connectionToSearch = null;
        if (array_key_exists('filters[email]', $queryParams)) {
            $emailToSearch = $queryParams['filters[email]'];
        }
        if (array_key_exists('filters[connectionid]', $queryParams)) {
            $connectionToSearch = $queryParams['filters[connectionid]'];
        }
        foreach (self::$activeCampaignResources as $activeCampaignResource) {
            if (($emailToSearch === null && $connectionToSearch === null) || ($emailToSearch === $activeCampaignResource['email'] && $connectionToSearch === $activeCampaignResource['connectionid'])) {
                $ecommerceCustomers[] = $activeCampaignResource['ecommerceCustomer'];
            }
        }

        return new ListEcommerceCustomersResponse($ecommerceCustomers);
    }

    public function update(int $activeCampaignResourceId, ResourceInterface $resource): UpdateResourceResponseInterface
    {
        return new UpdateEcommerceCustomerResponse(
            new EcommerceCustomerResponse(
                $activeCampaignResourceId
            )
        );
    }

    public function remove(int $activeCampaignResourceId): void
    {
        throw new RuntimeException('Not implemented');
    }
}
