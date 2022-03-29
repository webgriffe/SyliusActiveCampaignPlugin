<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusActiveCampaignPlugin\Integration\Client;

use Psr\Http\Message\RequestInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Webgriffe\SyliusActiveCampaignPlugin\Stub\HttpClientStub;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignResourceClient;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\Connection;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Connection\ConnectionResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Connection\CreateConnectionResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Connection\ListConnectionsResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Connection\UpdateConnectionResponse;

final class ActiveCampaignConnectionClientTest extends KernelTestCase
{
    private ActiveCampaignResourceClient $client;

    protected function setUp(): void
    {
        parent::setUp();
        HttpClientStub::setUp();

        $this->client = self::getContainer()->get('webgriffe.sylius_active_campaign_plugin.client.active_campaign.connection');
    }

    public function test_it_creates_connection_on_active_campaign(): void
    {
        HttpClientStub::$responseStatusCode = 201;
        HttpClientStub::$responseBodyContent = '{"connection":{"isInternal":0,"service":"fooCommerce","externalid":"toystore123","name":"Toystore, Inc.","logoUrl":"http:\/\/example.com\/i\/foo.png","linkUrl":"http:\/\/example.com\/foo\/","cdate":"2017-02-02T14:56:05-06:00","udate":"2017-02-02T14:56:05-06:00","links":{"customers":"\/connections\/1\/customers"},"id":"1"}}';
        $connection = new Connection('fooCommerce', 'toystore123', 'Toystore, Inc.', 'http://example.com/i/foo.png', 'http://example.com/foo/');

        $createdConnection = $this->client->create($connection);

        self::assertCount(1, HttpClientStub::$sentRequests);
        $sentRequest = reset(HttpClientStub::$sentRequests);
        self::assertInstanceOf(RequestInterface::class, $sentRequest);
        self::assertEquals('/api/3/connections', $sentRequest->getUri()->getPath());
        self::assertEquals('POST', $sentRequest->getMethod());
        self::assertEquals('{"connection":{"service":"fooCommerce","externalid":"toystore123","name":"Toystore, Inc.","logoUrl":"http:\/\/example.com\/i\/foo.png","linkUrl":"http:\/\/example.com\/foo\/"}}', $sentRequest->getBody()->getContents());

        self::assertNotNull($createdConnection);
        self::assertInstanceOf(CreateConnectionResponse::class, $createdConnection);
        self::assertEquals(1, $createdConnection->getResourceResponse()->getId());
    }

    public function test_it_lists_connections_on_active_campaign(): void
    {
        HttpClientStub::$responseStatusCode = 200;
        HttpClientStub::$responseBodyContent = '{"connections":[{"service":"shopify","externalid":"foo.myshopify.com","name":"Foo,Inc.","isInternal":"1","status":"1","syncStatus":"0","lastSync":"2017-02-02T13:09:07-06:00","logoUrl":"","linkUrl":"","cdate":"2017-02-02T13:09:07-06:00","udate":"2017-02-02T13:09:12-06:00","links":{"customers":"/api/3/connections/1/customers"},"id":"1"},{"service":"fooCommerce","externalid":"johndoe@example.com","name":"Acme,Inc.","isInternal":"0","status":"1","syncStatus":"0","lastSync":null,"logoUrl":"http://example.com/i/foo.png","linkUrl":"http://example.com/foo/","cdate":"2017-02-02T14:56:05-06:00","udate":"2017-02-03T15:54:51-06:00","links":{"customers":"/api/3/connections/2/customers"},"id":"2"}],"meta":{"total":"2"}}';

        $listConnectionsResponse = $this->client->list([
            'filters[service]' => 'sylius',
            'filters[externalid]' => 'ecommerce',
        ]);

        self::assertCount(1, HttpClientStub::$sentRequests);
        $sentRequest = reset(HttpClientStub::$sentRequests);
        self::assertInstanceOf(RequestInterface::class, $sentRequest);
        self::assertEquals('/api/3/connections', $sentRequest->getUri()->getPath());
        self::assertEquals('filters%5Bservice%5D=sylius&filters%5Bexternalid%5D=ecommerce', $sentRequest->getUri()->getQuery());
        self::assertEquals('GET', $sentRequest->getMethod());

        self::assertNotNull($listConnectionsResponse);
        self::assertInstanceOf(ListConnectionsResponse::class, $listConnectionsResponse);
        self::assertCount(2, $listConnectionsResponse->getResourceResponseLists());
        self::assertInstanceOf(ConnectionResponse::class, $listConnectionsResponse->getResourceResponseLists()[0]);
        self::assertEquals('1', $listConnectionsResponse->getResourceResponseLists()[0]->getId());
    }

    public function test_it_updates_connection_on_active_campaign(): void
    {
        HttpClientStub::$responseStatusCode = 200;
        HttpClientStub::$responseBodyContent = '{"connection":{"service":"fooCommerce","externalid":"johndoe@example.com","name":"Acme, Inc.","isInternal":"0","status":"1","syncStatus":"0","logoUrl":"http:\/\/foocorp.net\/i\/path3523.png","linkUrl":"http:\/\/example.com\/","cdate":"2017-02-02T14:56:05-06:00","udate":"2017-02-03T15:54:51-06:00","links":{"customers":"\/api\/3\/connections\/2\/customers"},"id":"2"}}';
        $connection = new Connection('fooCommerce', 'johndoe@example.com', 'Toystore, Inc.', 'http://example.com/i/foo.png', 'http://example.com/foo/');

        $updatedConnection = $this->client->update(2, $connection);

        self::assertCount(1, HttpClientStub::$sentRequests);
        $sentRequest = reset(HttpClientStub::$sentRequests);
        self::assertInstanceOf(RequestInterface::class, $sentRequest);
        self::assertEquals('/api/3/connections/2', $sentRequest->getUri()->getPath());
        self::assertEquals('PUT', $sentRequest->getMethod());
        self::assertEquals('{"connection":{"service":"fooCommerce","externalid":"johndoe@example.com","name":"Toystore, Inc.","logoUrl":"http:\/\/example.com\/i\/foo.png","linkUrl":"http:\/\/example.com\/foo\/"}}', $sentRequest->getBody()->getContents());

        self::assertNotNull($updatedConnection);
        self::assertInstanceOf(UpdateConnectionResponse::class, $updatedConnection);
        self::assertEquals(2, $updatedConnection->getResourceResponse()->getId());
    }

    public function test_it_removes_connection_on_active_campaign(): void
    {
        HttpClientStub::$responseStatusCode = 200;
        HttpClientStub::$responseBodyContent = '{}';

        $this->client->remove(2);

        self::assertCount(1, HttpClientStub::$sentRequests);
        $sentRequest = reset(HttpClientStub::$sentRequests);
        self::assertInstanceOf(RequestInterface::class, $sentRequest);
        self::assertEquals('/api/3/connections/2', $sentRequest->getUri()->getPath());
        self::assertEquals('DELETE', $sentRequest->getMethod());
        self::assertEmpty($sentRequest->getBody()->getContents());
    }
}
