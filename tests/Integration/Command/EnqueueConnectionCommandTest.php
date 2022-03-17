<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusActiveCampaignPlugin\Integration\Command;

use App\Entity\Channel\Channel;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Currency\Model\Currency;
use Sylius\Component\Locale\Model\Locale;
use Symfony\Component\Messenger\Envelope;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Connection\ConnectionCreate;

final class EnqueueConnectionCommandTest extends AbstractCommandTest
{
    private ChannelRepositoryInterface $channelRepository;

    private Locale $locale;

    private Currency $currency;

    protected function setUp(): void
    {
        parent::setUp();
        $entityManager = self::getContainer()->get('doctrine.orm.entity_manager');
        $purger = new ORMPurger($entityManager);
        $purger->purge();
        $this->channelRepository = self::getContainer()->get('sylius.repository.channel');

        $this->locale = new Locale();
        $this->locale->setCode('en_US');
        $entityManager->persist($this->locale);
        $this->currency = new Currency();
        $this->currency->setCode('EUR');
        $entityManager->persist($this->currency);
    }

    public function test_that_it_enqueues_connection(): void
    {
        $channel = $this->createChannel();
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
        $channel = $this->createChannel();
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
        $firstChannel = $this->createChannel('ecommerce');
        $secondChannel = $this->createChannel('support');
        $commandTester = $this->executeCommand([
            '--all' => true,
        ]);
        self::assertEquals(0, $commandTester->getStatusCode());

        $transport = self::getContainer()->get('messenger.transport.main');
        /** @var Envelope[] $messages */
        $messages = $transport->get();
        $this->assertCount(2, $messages);
        $message = $messages[0];
        $this->assertInstanceOf(ConnectionCreate::class, $message->getMessage());
        $this->assertEquals($firstChannel->getId(), $message->getMessage()->getChannelId());
        $message = $messages[1];
        $this->assertInstanceOf(ConnectionCreate::class, $message->getMessage());
        $this->assertEquals($secondChannel->getId(), $message->getMessage()->getChannelId());
    }

    protected function getCommandDefinition(): string
    {
        return 'webgriffe.sylius_active_campaign_plugin.command.enqueue_connection';
    }

    private function createChannel(string $code = 'ecommerce'): Channel
    {
        $channel = new Channel();
        $channel->setCode($code);
        $channel->setName('E Commerce');
        $channel->setTaxCalculationStrategy('order_items_based');
        $channel->setDefaultLocale($this->locale);
        $channel->setBaseCurrency($this->currency);
        $this->channelRepository->add($channel);

        return $channel;
    }
}
