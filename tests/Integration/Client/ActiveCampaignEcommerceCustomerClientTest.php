<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusActiveCampaignPlugin\Integration\Client;

use Psr\Http\Message\RequestInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Webgriffe\SyliusActiveCampaignPlugin\Stub\HttpClientStub;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignResourceClient;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\EcommerceCustomer;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\EcommerceCustomer\CreateEcommerceCustomerResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\EcommerceCustomer\EcommerceCustomerResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\EcommerceCustomer\ListEcommerceCustomersResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\EcommerceCustomer\UpdateEcommerceCustomerResponse;

final class ActiveCampaignEcommerceCustomerClientTest extends KernelTestCase
{
    private ActiveCampaignResourceClient $client;

    protected function setUp(): void
    {
        parent::setUp();
        HttpClientStub::setUp();

        $this->client = self::getContainer()->get('webgriffe.sylius_active_campaign_plugin.client.active_campaign.ecommerce_customer');
    }

    public function test_it_creates_ecommerce_customer_on_active_campaign(): void
    {
        HttpClientStub::$responseStatusCode = 201;
        HttpClientStub::$responseBodyContent = '{"ecomCustomer":{"connectionid":"1","externalid":"56789","email":"alice@example.com","links":{"connection":"/api/3/ecomCustomers/1/connection","orders":"/api/3/ecomCustomers/1/orders"},"id":"1","connection":"1"}}';
        $ecommerceCustomer = new EcommerceCustomer(
            'alice@example.com',
            '1',
            '56789',
            '1',
        );

        $createdEcommerceCustomer = $this->client->create($ecommerceCustomer);

        self::assertCount(1, HttpClientStub::$sentRequests);
        $sentRequest = reset(HttpClientStub::$sentRequests);
        self::assertInstanceOf(RequestInterface::class, $sentRequest);
        self::assertEquals('/api/3/ecomCustomers', $sentRequest->getUri()->getPath());
        self::assertEquals('POST', $sentRequest->getMethod());
        self::assertEquals(
            '{"ecomCustomer":{"email":"alice@example.com","connectionid":"1","externalid":"56789","acceptsMarketing":"1"}}',
            $sentRequest->getBody()->getContents(),
        );

        self::assertNotNull($createdEcommerceCustomer);
        self::assertInstanceOf(CreateEcommerceCustomerResponse::class, $createdEcommerceCustomer);
        self::assertEquals(1, $createdEcommerceCustomer->getResourceResponse()->getId());
    }

    public function test_it_lists_ecommerce_customers_on_active_campaign(): void
    {
        HttpClientStub::$responseStatusCode = 200;
        HttpClientStub::$responseBodyContent = '{"ecomCustomers":[{"connectionid":"1","externalid":"56789","email":"alice@example.com","totalRevenue":"3280","totalOrders":"2","totalProducts":"2","avgRevenuePerOrder":"2285","avgProductCategory":"Electronics","tstamp":"2017-02-06T14:05:31-06:00","links":{"connection":"/api/3/ecomCustomers/1/connection","orders":"/api/3/ecomCustomers/1/orders"},"id":"1","connection":"1"},{"connectionid":"2","externalid":"44322","email":"alice@example.com","totalRevenue":"7599","totalOrders":"1","totalProducts":"1","avgRevenuePerOrder":"7599","avgProductCategory":"Books","tstamp":"2016-12-13T18:02:07-06:00","links":{"connection":"/api/3/ecomCustomers/3/connection","orders":"/api/3/ecomCustomers/3/orders"},"id":"3","connection":"2"},{"connectionid":"0","externalid":"0","email":"alice@example.com","totalRevenue":"10879","totalOrders":"3","totalProducts":"3","avgRevenuePerOrder":"3626","avgProductCategory":"Electronics","tstamp":"2017-02-06T14:05:31-06:00","links":{"connection":"/api/3/ecomCustomers/2/connection","orders":"/api/3/ecomCustomers/2/orders"},"id":"2","connection":null}],"meta":{"total":"3"}}';

        $listEcommerceCustomersResponse = $this->client->list([
            'filters[email]' => 'test@email.com',
            'filters[connectionid]' => '4',
        ]);

        self::assertCount(1, HttpClientStub::$sentRequests);
        $sentRequest = reset(HttpClientStub::$sentRequests);
        self::assertInstanceOf(RequestInterface::class, $sentRequest);
        self::assertEquals('/api/3/ecomCustomers', $sentRequest->getUri()->getPath());
        self::assertEquals('filters%5Bemail%5D=test%40email.com&filters%5Bconnectionid%5D=4', $sentRequest->getUri()->getQuery());
        self::assertEquals('GET', $sentRequest->getMethod());

        self::assertNotNull($listEcommerceCustomersResponse);
        self::assertInstanceOf(ListEcommerceCustomersResponse::class, $listEcommerceCustomersResponse);
        self::assertCount(3, $listEcommerceCustomersResponse->getResourceResponseLists());
        self::assertInstanceOf(EcommerceCustomerResponse::class, $listEcommerceCustomersResponse->getResourceResponseLists()[0]);
        self::assertEquals('1', $listEcommerceCustomersResponse->getResourceResponseLists()[0]->getId());
    }

    public function test_it_updates_ecommerce_customer_on_active_campaign(): void
    {
        HttpClientStub::$responseStatusCode = 200;
        HttpClientStub::$responseBodyContent = '{"ecomCustomer":{"connectionid":"1","externalid":"98765","email":"alice@example.com","totalRevenue":"3280","totalOrders":"2","totalProducts":"2","avgRevenuePerOrder":"2285","avgProductCategory":"Electronics","tstamp":"2017-02-06T14:05:31-06:00","links":{"connection":"/api/3/ecomCustomers/1/connection","orders":"/api/3/ecomCustomers/1/orders"},"id":"1","connection":"1"}}';
        $ecommerceCustomer = new EcommerceCustomer(
            'alice@example.com',
            '1',
            '98765',
            '1',
        );

        $updatedEcommerceCustomer = $this->client->update(1, $ecommerceCustomer);

        self::assertCount(1, HttpClientStub::$sentRequests);
        $sentRequest = reset(HttpClientStub::$sentRequests);
        self::assertInstanceOf(RequestInterface::class, $sentRequest);
        self::assertEquals('/api/3/ecomCustomers/1', $sentRequest->getUri()->getPath());
        self::assertEquals('PUT', $sentRequest->getMethod());
        self::assertEquals(
            '{"ecomCustomer":{"email":"alice@example.com","connectionid":"1","externalid":"98765","acceptsMarketing":"1"}}',
            $sentRequest->getBody()->getContents(),
        );

        self::assertNotNull($updatedEcommerceCustomer);
        self::assertInstanceOf(UpdateEcommerceCustomerResponse::class, $updatedEcommerceCustomer);
        self::assertEquals(1, $updatedEcommerceCustomer->getResourceResponse()->getId());
    }

    public function test_it_removes_ecommerce_customer_on_active_campaign(): void
    {
        HttpClientStub::$responseStatusCode = 200;
        HttpClientStub::$responseBodyContent = '{}';

        $this->client->remove(1);

        self::assertCount(1, HttpClientStub::$sentRequests);
        $sentRequest = reset(HttpClientStub::$sentRequests);
        self::assertInstanceOf(RequestInterface::class, $sentRequest);
        self::assertEquals('/api/3/ecomCustomers/1', $sentRequest->getUri()->getPath());
        self::assertEquals('DELETE', $sentRequest->getMethod());
        self::assertEmpty($sentRequest->getBody()->getContents());
    }
}
