<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusActiveCampaignPlugin\Integration\Client;

use Psr\Http\Message\RequestInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Webgriffe\SyliusActiveCampaignPlugin\Stub\HttpClientStub;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignResourceClient;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\Tag;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\TagInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Tag\CreateTagResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Tag\ListTagsResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Tag\TagResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Tag\UpdateTagResponse;

final class ActiveCampaignTagClientTest extends KernelTestCase
{
    private ActiveCampaignResourceClient $client;

    protected function setUp(): void
    {
        parent::setUp();
        HttpClientStub::setUp();

        $this->client = self::getContainer()->get('webgriffe.sylius_active_campaign_plugin.client.active_campaign.tag');
    }

    public function test_it_creates_tag_on_active_campaign(): void
    {
        HttpClientStub::$responseStatusCode = 201;
        HttpClientStub::$responseBodyContent = '{"tag":{"tag":"My Tag","description":"Description","tagType":"contact","cdate":"2018-09-29T19:21:25-05:00","links":{"contactGoalTags":"https://:account.api-us1.com/api/:version/tags/16/contactGoalTags"},"id":"16"}}';
        $tag = new Tag('My Tag', TagInterface::CONTACT_TAG_TYPE, 'Description');

        $createdTag = $this->client->create($tag);

        self::assertCount(1, HttpClientStub::$sentRequests);
        $sentRequest = reset(HttpClientStub::$sentRequests);
        self::assertInstanceOf(RequestInterface::class, $sentRequest);
        self::assertEquals('/api/3/tags', $sentRequest->getUri()->getPath());
        self::assertEquals('POST', $sentRequest->getMethod());
        self::assertEquals('{"tag":{"tag":"My Tag","tagType":"contact","description":"Description"}}', $sentRequest->getBody()->getContents());

        self::assertNotNull($createdTag);
        self::assertInstanceOf(CreateTagResponse::class, $createdTag);
        self::assertEquals(16, $createdTag->getResourceResponse()->getId());
    }

    public function test_it_lists_tags_on_active_campaign(): void
    {
        HttpClientStub::$responseStatusCode = 200;
        HttpClientStub::$responseBodyContent = '{"tags":[{"tagType":"contact","tag":"one","description":"","cdate":"2018-08-17T09:43:15-05:00","links":{"contactGoalTags":"https://:account.api-us1.com/api/:version/tags/1/contactGoalTags"},"id":"1"},{"tagType":"contact","tag":"two","description":"","cdate":"2018-08-17T13:41:16-05:00","links":{"contactGoalTags":"https://:account.api-us1.com/api/:version/tags/2/contactGoalTags"},"id":"2"},{"tagType":"contact","tag":"three","description":"","cdate":"2018-08-17T13:41:18-05:00","links":{"contactGoalTags":"https://:account.api-us1.com/api/:version/tags/3/contactGoalTags"},"id":"3"},{"tagType":"template","tag":"test1","description":"","cdate":"2018-08-28T11:54:36-05:00","links":{"contactGoalTags":"https://:account.api-us1.com/api/:version/tags/4/contactGoalTags"},"id":"4"},{"tagType":"template","tag":"test2","description":"","cdate":"2018-08-28T11:54:38-05:00","links":{"contactGoalTags":"https://:account.api-us1.com/api/:version/tags/5/contactGoalTags"},"id":"5"}],"meta":{"total":"5"}}';

        $listTagsResponse = $this->client->list(['search' => 'one']);

        self::assertCount(1, HttpClientStub::$sentRequests);
        $sentRequest = reset(HttpClientStub::$sentRequests);
        self::assertInstanceOf(RequestInterface::class, $sentRequest);
        self::assertEquals('/api/3/tags', $sentRequest->getUri()->getPath());
        self::assertEquals('search=one', $sentRequest->getUri()->getQuery());
        self::assertEquals('GET', $sentRequest->getMethod());

        self::assertNotNull($listTagsResponse);
        self::assertInstanceOf(ListTagsResponse::class, $listTagsResponse);
        self::assertCount(5, $listTagsResponse->getResourceResponseLists());
        self::assertInstanceOf(TagResponse::class, $listTagsResponse->getResourceResponseLists()[0]);
        self::assertEquals('1', $listTagsResponse->getResourceResponseLists()[0]->getId());
    }

    public function test_it_updates_tag_on_active_campaign(): void
    {
        HttpClientStub::$responseStatusCode = 200;
        HttpClientStub::$responseBodyContent = '{"tag":{"tagType":"contact","tag":"My Tag","description":"Description","cdate":"2018-08-17T13:41:16-05:00","links":{"contactGoalTags":"https://:account.api-us1.com/api/:version/tags/2/contactGoalTags"},"id":"2"}}';
        $tag = new Tag('My Tag', TagInterface::CONTACT_TAG_TYPE, 'Description');

        $updatedTag = $this->client->update(2, $tag);

        self::assertCount(1, HttpClientStub::$sentRequests);
        $sentRequest = reset(HttpClientStub::$sentRequests);
        self::assertInstanceOf(RequestInterface::class, $sentRequest);
        self::assertEquals('/api/3/tags/2', $sentRequest->getUri()->getPath());
        self::assertEquals('PUT', $sentRequest->getMethod());
        self::assertEquals('{"tag":{"tag":"My Tag","tagType":"contact","description":"Description"}}', $sentRequest->getBody()->getContents());

        self::assertNotNull($updatedTag);
        self::assertInstanceOf(UpdateTagResponse::class, $updatedTag);
        self::assertEquals(2, $updatedTag->getResourceResponse()->getId());
    }

    public function test_it_removes_tag_on_active_campaign(): void
    {
        HttpClientStub::$responseStatusCode = 200;
        HttpClientStub::$responseBodyContent = '{}';

        $this->client->remove(2);

        self::assertCount(1, HttpClientStub::$sentRequests);
        $sentRequest = reset(HttpClientStub::$sentRequests);
        self::assertInstanceOf(RequestInterface::class, $sentRequest);
        self::assertEquals('/api/3/tags/2', $sentRequest->getUri()->getPath());
        self::assertEquals('DELETE', $sentRequest->getMethod());
        self::assertEmpty($sentRequest->getBody()->getContents());
    }
}
