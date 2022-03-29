<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusActiveCampaignPlugin\Integration\Command;

use Fidry\AliceDataFixtures\Persistence\PurgeMode;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Symfony\Component\Messenger\Envelope;
use Tests\Webgriffe\SyliusActiveCampaignPlugin\Stub\ActiveCampaignConnectionClientStub;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Connection\ConnectionCreate;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Connection\ConnectionUpdate;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Connection\ConnectionResponse;

final class EnqueueConnectionCommandTest extends AbstractCommandTest
{
    private const FIXTURE_BASE_DIR = __DIR__ . '/../../DataFixtures/ORM/resources/Command/EnqueueConnectionCommandTest';

    private ChannelRepositoryInterface $channelRepository;

    protected function setUp(): void
    {
        parent::setUp();
        ActiveCampaignConnectionClientStub::$activeCampaignResources = [
            [
                'service' => 'sylius',
                'externalid' => 'other_shop',
                'connection' => new ConnectionResponse(132),
            ],
        ];
        $this->channelRepository = self::getContainer()->get('sylius.repository.channel');

        $fixtureLoader = self::getContainer()->get('fidry_alice_data_fixtures.loader.doctrine');
        $fixtureLoader->load([
            self::FIXTURE_BASE_DIR . '/channels.yaml',
        ], [], [], PurgeMode::createDeleteMode());
    }

    public function test_that_it_enqueues_connection(): void
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
        $this->assertInstanceOf(ConnectionCreate::class, $message->getMessage());
        $this->assertEquals($channel->getId(), $message->getMessage()->getChannelId());
    }

    public function test_that_it_enqueues_connection_interactively(): void
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
        $this->assertInstanceOf(ConnectionCreate::class, $message->getMessage());
        $this->assertEquals($channel->getId(), $message->getMessage()->getChannelId());
    }

    public function test_that_it_enqueues_all_contacts(): void
    {
        $commandTester = $this->executeCommand([
            '--all' => true,
        ]);
        self::assertEquals(0, $commandTester->getStatusCode());

        $fashionChannel = $this->channelRepository->findOneBy(['code' => 'fashion_shop']);
        $digitalChannel = $this->channelRepository->findOneBy(['code' => 'digital_shop']);
        $otherChannel = $this->channelRepository->findOneBy(['code' => 'other_shop']);
        $transport = self::getContainer()->get('messenger.transport.main');
        /** @var Envelope[] $messages */
        $messages = $transport->get();
        $this->assertCount(3, $messages);
        $message = $messages[0];
        $this->assertInstanceOf(ConnectionCreate::class, $message->getMessage());
        $this->assertEquals($fashionChannel->getId(), $message->getMessage()->getChannelId());

        $message = $messages[1];
        $this->assertInstanceOf(ConnectionUpdate::class, $message->getMessage());
        $this->assertEquals($digitalChannel->getId(), $message->getMessage()->getChannelId());
        $this->assertEquals(12, $message->getMessage()->getActiveCampaignId());

        $message = $messages[2];
        $this->assertInstanceOf(ConnectionUpdate::class, $message->getMessage());
        $this->assertEquals($otherChannel->getId(), $message->getMessage()->getChannelId());
        $this->assertEquals(132, $message->getMessage()->getActiveCampaignId());
    }

    protected function getCommandDefinition(): string
    {
        return 'webgriffe.sylius_active_campaign_plugin.command.enqueue_connection';
    }
}
