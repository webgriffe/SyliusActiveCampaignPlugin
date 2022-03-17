<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusActiveCampaignPlugin\Integration\EventSubscriber;

use App\Entity\Channel\Channel;
use App\Entity\Channel\ChannelInterface;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Currency\Model\Currency;
use Sylius\Component\Locale\Model\Locale;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\InMemoryTransport;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Connection\ConnectionCreate;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Connection\ConnectionRemove;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Connection\ConnectionUpdate;

final class ChannelSubscriberTest extends AbstractEventDispatcherTest
{
    private ChannelRepositoryInterface $channelRepository;

    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->entityManager = self::getContainer()->get('doctrine.orm.entity_manager');
        $this->channelRepository = self::getContainer()->get('sylius.repository.channel');
    }

    public function test_that_it_creates_connection_on_active_campaign(): void
    {
        $channel = $this->createChannel();
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

    public function test_that_it_updates_channel_on_active_campaign(): void
    {
        $channel = $this->createChannel();
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

    public function test_that_it_removes_channel_on_active_campaign(): void
    {
        $channel = $this->createChannel();
        $channel->setActiveCampaignId(15);
        $this->channelRepository->add($channel);
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

    private function createChannel(): ChannelInterface
    {
        $locale = new Locale();
        $locale->setCode('en_US');
        $this->entityManager->persist($locale);
        $currency = new Currency();
        $currency->setCode('EUR');
        $this->entityManager->persist($currency);
        $channel = new Channel();
        $channel->setCode('ecommerce');
        $channel->setName('E Commerce');
        $channel->setTaxCalculationStrategy('order_items_based');
        $channel->setDefaultLocale($locale);
        $channel->setBaseCurrency($currency);
        $this->channelRepository->add($channel);

        return $channel;
    }
}
