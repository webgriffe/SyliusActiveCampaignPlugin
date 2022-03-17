<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusActiveCampaignPlugin\Integration\Client;

use Psr\Http\Message\RequestInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Webgriffe\SyliusActiveCampaignPlugin\Stub\HttpClientStub;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignResourceClient;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\Connection;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Connection\CreateConnectionResponse;
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
        self::assertEquals('0', $createdConnection->getConnection()->getIsInternal());
        self::assertEquals('fooCommerce', $createdConnection->getConnection()->getService());
        self::assertEquals('toystore123', $createdConnection->getConnection()->getExternalId());
        self::assertEquals('Toystore, Inc.', $createdConnection->getConnection()->getName());
        self::assertEquals('http://example.com/i/foo.png', $createdConnection->getConnection()->getLogoUrl());
        self::assertEquals('http://example.com/foo/', $createdConnection->getConnection()->getLinkUrl());
        self::assertEquals('2017-02-02T14:56:05-06:00', $createdConnection->getConnection()->getCreatedAt());
        self::assertEquals('2017-02-02T14:56:05-06:00', $createdConnection->getConnection()->getUpdatedAt());
        self::assertCount(1, $createdConnection->getConnection()->getLinks());
        self::assertEquals(1, $createdConnection->getConnection()->getId());
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
        self::assertEquals('fooCommerce', $updatedConnection->getConnection()->getService());
        self::assertEquals('johndoe@example.com', $updatedConnection->getConnection()->getExternalId());
        self::assertEquals('Acme, Inc.', $updatedConnection->getConnection()->getName());
        self::assertEquals('0', $updatedConnection->getConnection()->getIsInternal());
        self::assertEquals('1', $updatedConnection->getConnection()->getStatus());
        self::assertEquals('0', $updatedConnection->getConnection()->getSyncStatus());
        self::assertEquals('http://foocorp.net/i/path3523.png', $updatedConnection->getConnection()->getLogoUrl());
        self::assertEquals('http://example.com/', $updatedConnection->getConnection()->getLinkUrl());
        self::assertEquals('2017-02-02T14:56:05-06:00', $updatedConnection->getConnection()->getCreatedAt());
        self::assertEquals('2017-02-03T15:54:51-06:00', $updatedConnection->getConnection()->getUpdatedAt());
        self::assertCount(1, $updatedConnection->getConnection()->getLinks());
        self::assertEquals(2, $updatedConnection->getConnection()->getId());
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
