<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusActiveCampaignPlugin\Integration\EventSubscriber;

use Fidry\AliceDataFixtures\Persistence\PurgeMode;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\InMemoryTransport;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Connection\ConnectionCreate;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Connection\ConnectionRemove;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Connection\ConnectionUpdate;

final class ChannelSubscriberTest extends AbstractEventDispatcherTest
{
    private const FIXTURE_BASE_DIR = __DIR__ . '/../../DataFixtures/ORM/resources/EventSubscriber/ChannelSubscriberTest';

    private ChannelRepositoryInterface $channelRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->channelRepository = self::getContainer()->get('sylius.repository.channel');

        $fixtureLoader = self::getContainer()->get('fidry_alice_data_fixtures.loader.doctrine');
        $fixtureLoader->load([
            self::FIXTURE_BASE_DIR . '/channels.yaml',
        ], [], [], PurgeMode::createDeleteMode());
    }

    public function test_that_it_creates_connection_on_active_campaign(): void
    {
        $channel = $this->channelRepository->findOneBy(['code' => 'fashion_shop']);
        $this->eventDispatcher->dispatch(new ResourceControllerEvent($channel), 'sylius.channel.post_create');
        /** @var InMemoryTransport $transport */
        $transport = self::getContainer()->get('messenger.transport.main');
        /** @var Envelope[] $messages */
        $messages = $transport->get();
        $this->assertCount(1, $messages);
        $message = $messages[0];
        $this->assertInstanceOf(ConnectionCreate::class, $message->getMessage());
        $this->assertEquals($channel->getId(), $message->getMessage()->getChannelId());
    }

    public function test_that_it_updates_connection_on_active_campaign(): void
    {
        $channel = $this->channelRepository->findOneBy(['code' => 'fashion_shop']);
        $channel->setActiveCampaignId(15);
        $this->channelRepository->add($channel);
        $this->eventDispatcher->dispatch(new ResourceControllerEvent($channel), 'sylius.channel.post_update');
        /** @var InMemoryTransport $transport */
        $transport = self::getContainer()->get('messenger.transport.main');
        /** @var Envelope[] $messages */
        $messages = $transport->get();
        $this->assertCount(1, $messages);
        $message = $messages[0];
        $this->assertInstanceOf(ConnectionUpdate::class, $message->getMessage());
        $this->assertEquals($channel->getId(), $message->getMessage()->getChannelId());
        $this->assertEquals($channel->getActiveCampaignId(), $message->getMessage()->getActiveCampaignId());
    }

    public function test_that_it_removes_connection_on_active_campaign(): void
    {
        $channel = $this->channelRepository->findOneBy(['code' => 'fashion_shop']);
        $this->eventDispatcher->dispatch(new ResourceControllerEvent($channel), 'sylius.channel.post_delete');
        /** @var InMemoryTransport $transport */
        $transport = self::getContainer()->get('messenger.transport.main');
        /** @var Envelope[] $messages */
        $messages = $transport->get();
        $this->assertCount(1, $messages);
        $message = $messages[0];
        $this->assertInstanceOf(ConnectionRemove::class, $message->getMessage());
        $this->assertEquals($channel->getActiveCampaignId(), $message->getMessage()->getActiveCampaignId());
    }
}
