<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusActiveCampaignPlugin\Integration\Client;

use Psr\Http\Message\RequestInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Webgriffe\SyliusActiveCampaignPlugin\Stub\HttpClientStub;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignResourceClient;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\Webhook;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Webhook\CreateWebhookResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Webhook\ListWebhooksResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Webhook\UpdateWebhookResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Webhook\WebhookResponse;

final class ActiveCampaignWebhookClientTest extends KernelTestCase
{
    private ActiveCampaignResourceClient $client;

    protected function setUp(): void
    {
        parent::setUp();
        HttpClientStub::setUp();

        $this->client = self::getContainer()->get('webgriffe.sylius_active_campaign_plugin.client.active_campaign.webhook');
    }

    public function test_it_creates_webhook_on_active_campaign(): void
    {
        HttpClientStub::$responseStatusCode = 201;
        HttpClientStub::$responseBodyContent = '{"webhook":{"cdate":"2016-01-01T12:00:00-00:00","listid":"0","name":"My Hook","url":"http://example.com/my-hook","events":["subscribe","unsubscribe","sent"],"sources":["public","system"],"links":[],"id":"1"}}';
        $webhook = new Webhook('My Hook', 'http://example.com/my-hook', ['subscribe', 'unsubscribe', 'sent'], ['public', 'system']);

        $createdWebhook = $this->client->create($webhook);

        self::assertCount(1, HttpClientStub::$sentRequests);
        $sentRequest = reset(HttpClientStub::$sentRequests);
        self::assertInstanceOf(RequestInterface::class, $sentRequest);
        self::assertEquals('/api/3/webhooks', $sentRequest->getUri()->getPath());
        self::assertEquals('POST', $sentRequest->getMethod());
        self::assertEquals('{"webhook":{"name":"My Hook","url":"http:\/\/example.com\/my-hook","events":["subscribe","unsubscribe","sent"],"sources":["public","system"],"listid":null}}', $sentRequest->getBody()->getContents());

        self::assertNotNull($createdWebhook);
        self::assertInstanceOf(CreateWebhookResponse::class, $createdWebhook);
        self::assertEquals(1, $createdWebhook->getResourceResponse()->getId());
    }

    public function test_it_lists_webhooks_on_active_campaign(): void
    {
        HttpClientStub::$responseStatusCode = 200;
        HttpClientStub::$responseBodyContent = '{"webhooks":[{"cdate":"2016-01-01T12:00:00-00:00","listid":"0","name":"MyHook","url":"http://example.com/my-hook","events":["subscribe","unsubscribe","sent"],"sources":["public","system"],"links":[],"id":"1"},{"cdate":"2016-01-01T12:00:00-00:00","listid":"0","name":"MyHook2","url":"http://example.com/my-hook-2","events":["subscribe"],"sources":["admin"],"links":[],"id":"2"}],"meta":{"total":"2"}}';

        $listWebhooksResponse = $this->client->list(['filters[name]' => 'list']);

        self::assertCount(1, HttpClientStub::$sentRequests);
        $sentRequest = reset(HttpClientStub::$sentRequests);
        self::assertInstanceOf(RequestInterface::class, $sentRequest);
        self::assertEquals('/api/3/webhooks', $sentRequest->getUri()->getPath());
        self::assertEquals('filters%5Bname%5D=list', $sentRequest->getUri()->getQuery());
        self::assertEquals('GET', $sentRequest->getMethod());

        self::assertNotNull($listWebhooksResponse);
        self::assertInstanceOf(ListWebhooksResponse::class, $listWebhooksResponse);
        self::assertCount(2, $listWebhooksResponse->getResourceResponseLists());
        self::assertInstanceOf(WebhookResponse::class, $listWebhooksResponse->getResourceResponseLists()[0]);
        self::assertEquals('1', $listWebhooksResponse->getResourceResponseLists()[0]->getId());
    }

    public function test_it_updates_webhook_on_active_campaign(): void
    {
        HttpClientStub::$responseStatusCode = 200;
        HttpClientStub::$responseBodyContent = '{"webhook":{"cdate":"2016-01-01T12:00:00-00:00","listid":"0","name":"My Hook","url":"http://example.com/my-hook","events":["subscribe","unsubscribe","sent"],"sources":["public","system"],"links":[],"id":"1"}}';
        $webhook = new Webhook('My Hook', 'http://example.com/my-hook', ['subscribe', 'unsubscribe', 'sent'], ['public', 'system']);

        $updatedWebhook = $this->client->update(1, $webhook);

        self::assertCount(1, HttpClientStub::$sentRequests);
        $sentRequest = reset(HttpClientStub::$sentRequests);
        self::assertInstanceOf(RequestInterface::class, $sentRequest);
        self::assertEquals('/api/3/webhooks/1', $sentRequest->getUri()->getPath());
        self::assertEquals('PUT', $sentRequest->getMethod());
        self::assertEquals('{"webhook":{"name":"My Hook","url":"http:\/\/example.com\/my-hook","events":["subscribe","unsubscribe","sent"],"sources":["public","system"],"listid":null}}', $sentRequest->getBody()->getContents());

        self::assertNotNull($updatedWebhook);
        self::assertInstanceOf(UpdateWebhookResponse::class, $updatedWebhook);
        self::assertEquals(1, $updatedWebhook->getResourceResponse()->getId());
    }

    public function test_it_removes_webhook_on_active_campaign(): void
    {
        HttpClientStub::$responseStatusCode = 200;
        HttpClientStub::$responseBodyContent = '{}';

        $this->client->remove(2);

        self::assertCount(1, HttpClientStub::$sentRequests);
        $sentRequest = reset(HttpClientStub::$sentRequests);
        self::assertInstanceOf(RequestInterface::class, $sentRequest);
        self::assertEquals('/api/3/webhooks/2', $sentRequest->getUri()->getPath());
        self::assertEquals('DELETE', $sentRequest->getMethod());
        self::assertEmpty($sentRequest->getBody()->getContents());
    }
}
