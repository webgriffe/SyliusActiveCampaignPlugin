<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusActiveCampaignPlugin\Integration\Command;

use Fidry\AliceDataFixtures\Persistence\PurgeMode;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Symfony\Component\Messenger\Envelope;
use Tests\Webgriffe\SyliusActiveCampaignPlugin\Stub\ActiveCampaignWebhookClientStub;
use Tests\Webgriffe\SyliusActiveCampaignPlugin\Stub\HttpClientStub;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Webhook\WebhookCreate;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Webhook\WebhookResponse;

final class EnqueueWebhookCommandTest extends AbstractCommandTest
{
    private const FIXTURE_BASE_DIR = __DIR__ . '/../../DataFixtures/ORM/resources/Command/EnqueueWebhookCommandTest';

    private ChannelRepositoryInterface $channelRepository;

    protected function setUp(): void
    {
        parent::setUp();
        HttpClientStub::$responseStatusCode = 201;
        ActiveCampaignWebhookClientStub::$activeCampaignResources = [
            [
                'url' => 'https://other.com/webhook/activecampaign/list-status',
                'listid' => '43',
                'webhook' => new WebhookResponse(45),
            ],
        ];
        $this->channelRepository = self::getContainer()->get('sylius.repository.channel');

        $fixtureLoader = self::getContainer()->get('fidry_alice_data_fixtures.loader.doctrine');
        $fixtureLoader->load([
            self::FIXTURE_BASE_DIR . '/channels.yaml',
        ], [], [], PurgeMode::createDeleteMode());
    }

    public function test_that_it_enqueues_webhook(): void
    {
        $channel = $this->channelRepository->findOneBy(['code' => 'fashion_shop']);
        self::assertNotNull($channel->getId());
        $commandTester = $this->executeCommand([
            'channel-id' => $channel->getId(),
        ], []);
        self::assertEquals(0, $commandTester->getStatusCode());

        $transport = self::getContainer()->get('messenger.transport.main');
        /** @var Envelope[] $messages */
        $messages = $transport->get();
        $this->assertCount(1, $messages);
        $message = $messages[0];
        $this->assertInstanceOf(WebhookCreate::class, $message->getMessage());
        $this->assertEquals($channel->getId(), $message->getMessage()->getChannelId());
    }

    public function test_that_it_enqueues_webhook_interactively(): void
    {
        $channel = $this->channelRepository->findOneBy(['code' => 'fashion_shop']);
        self::assertNotNull($channel->getId());
        $commandTester = $this->executeCommand([], [
            $channel->getId(),
        ]);
        self::assertEquals(0, $commandTester->getStatusCode());

        $transport = self::getContainer()->get('messenger.transport.main');
        /** @var Envelope[] $messages */
        $messages = $transport->get();
        $this->assertCount(1, $messages);
        $message = $messages[0];
        $this->assertInstanceOf(WebhookCreate::class, $message->getMessage());
        $this->assertEquals($channel->getId(), $message->getMessage()->getChannelId());
    }

    public function test_that_it_enqueues_all_webhooks(): void
    {
        $commandTester = $this->executeCommand([
            '--all' => true,
        ]);
        self::assertEquals(0, $commandTester->getStatusCode());

        $fashionChannel = $this->channelRepository->findOneBy(['code' => 'fashion_shop']);
        $digitalChannel = $this->channelRepository->findOneBy(['code' => 'digital_shop']);
        $transport = self::getContainer()->get('messenger.transport.main');
        /** @var Envelope[] $messages */
        $messages = $transport->get();
        $this->assertCount(2, $messages);
        $message = $messages[0];
        $this->assertInstanceOf(WebhookCreate::class, $message->getMessage());
        $this->assertEquals($fashionChannel->getId(), $message->getMessage()->getChannelId());

        $message = $messages[1];
        $this->assertInstanceOf(WebhookCreate::class, $message->getMessage());
        $this->assertEquals($digitalChannel->getId(), $message->getMessage()->getChannelId());
    }

    protected function getCommandDefinition(): string
    {
        return 'webgriffe.sylius_active_campaign_plugin.command.enqueue_webhook';
    }
}
