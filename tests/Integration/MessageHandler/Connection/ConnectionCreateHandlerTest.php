<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusActiveCampaignPlugin\Integration\MessageHandler\Connection;

use App\Entity\Channel\ChannelInterface;
use Fidry\AliceDataFixtures\Persistence\PurgeMode;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Connection\ConnectionCreate;
use Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\Connection\ConnectionCreateHandler;

final class ConnectionCreateHandlerTest extends KernelTestCase
{
    private const FIXTURE_BASE_DIR = __DIR__ . '/../../../DataFixtures/ORM/resources/MessageHandler/ConnectionCreateHandlerTest';

    private ChannelRepositoryInterface $channelRepository;

    private ConnectionCreateHandler $connectionCreateHandler;

    protected function setUp(): void
    {
        parent::setUp();
        $this->channelRepository = self::getContainer()->get('sylius.repository.channel');

        $fixtureLoader = self::getContainer()->get('fidry_alice_data_fixtures.loader.doctrine');
        $fixtureLoader->load([
            self::FIXTURE_BASE_DIR . '/channels.yaml',
        ], [], [], PurgeMode::createDeleteMode());

        $this->connectionCreateHandler = new ConnectionCreateHandler(
            self::getContainer()->get('webgriffe.sylius_active_campaign_plugin.mapper.connection'),
            self::getContainer()->get('webgriffe.sylius_active_campaign_plugin.client_stub.active_campaign.connection'),
            $this->channelRepository,
        );
    }

    public function test_that_it_creates_connection_on_active_campaign(): void
    {
        $channel = $this->channelRepository->findOneByCode('fashion_shop');
        $this->connectionCreateHandler->__invoke(new ConnectionCreate($channel->getId()));

        /** @var ChannelInterface|null $channel */
        $channel = $this->channelRepository->find($channel->getId());
        $this->assertEquals(1, $channel->getActiveCampaignId());
    }
}
