<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusActiveCampaignPlugin\Integration\Client;

use Psr\Http\Message\RequestInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Webgriffe\SyliusActiveCampaignPlugin\Stub\HttpClientStub;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignResourceClient;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ContactTag;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ContactTag\CreateContactTagResponse;

final class ActiveCampaignContactTagClientTest extends KernelTestCase
{
    private ActiveCampaignResourceClient $client;

    protected function setUp(): void
    {
        parent::setUp();
        HttpClientStub::setUp();

        $this->client = self::getContainer()->get('webgriffe.sylius_active_campaign_plugin.client.active_campaign.contact_tag');
    }

    public function test_it_creates_contact_tag_on_active_campaign(): void
    {
        HttpClientStub::$responseStatusCode = 201;
        HttpClientStub::$responseBodyContent = '{"contactTag":{"cdate":"2017-06-08T16:11:53-05:00","contact":"1","id":"1","links":{"contact":"/1/contact","tag":"/1/tag"},"tag":"20"}}';
        $contactTag = new ContactTag(1, 20);

        $createdContactTag = $this->client->create($contactTag);

        self::assertCount(1, HttpClientStub::$sentRequests);
        $sentRequest = reset(HttpClientStub::$sentRequests);
        self::assertInstanceOf(RequestInterface::class, $sentRequest);
        self::assertEquals('/api/3/contactTags', $sentRequest->getUri()->getPath());
        self::assertEquals('POST', $sentRequest->getMethod());
        self::assertEquals('{"contactTag":{"contact":1,"tag":20}}', $sentRequest->getBody()->getContents());

        self::assertNotNull($createdContactTag);
        self::assertInstanceOf(CreateContactTagResponse::class, $createdContactTag);
        self::assertEquals(1, $createdContactTag->getResourceResponse()->getId());
    }

    public function test_it_removes_contact_tag_on_active_campaign(): void
    {
        HttpClientStub::$responseStatusCode = 200;
        HttpClientStub::$responseBodyContent = '{}';

        $this->client->remove(2);

        self::assertCount(1, HttpClientStub::$sentRequests);
        $sentRequest = reset(HttpClientStub::$sentRequests);
        self::assertInstanceOf(RequestInterface::class, $sentRequest);
        self::assertEquals('/api/3/contactTags/2', $sentRequest->getUri()->getPath());
        self::assertEquals('DELETE', $sentRequest->getMethod());
        self::assertEmpty($sentRequest->getBody()->getContents());
    }
}
