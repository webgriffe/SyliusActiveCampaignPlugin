<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusActiveCampaignPlugin\Integration\MessageHandler\Connection;

use App\Entity\Channel\Channel;
use App\Entity\Channel\ChannelInterface;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Currency\Model\Currency;
use Sylius\Component\Locale\Model\Locale;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Webgriffe\SyliusActiveCampaignPlugin\Stub\ActiveCampaignConnectionClientStub;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Connection\ConnectionCreate;
use Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\Connection\ConnectionCreateHandler;

final class ConnectionCreateHandlerTest extends KernelTestCase
{
    private ChannelRepositoryInterface $channelRepository;

    private ConnectionCreateHandler $connectionCreateHandler;

    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->entityManager = self::getContainer()->get('doctrine.orm.entity_manager');
        $purger = new ORMPurger($this->entityManager);
        $purger->purge();
        $this->channelRepository = self::getContainer()->get('sylius.repository.channel');
        $this->connectionCreateHandler = new ConnectionCreateHandler(
            self::getContainer()->get('webgriffe.sylius_active_campaign_plugin.mapper.connection'),
            new ActiveCampaignConnectionClientStub(),
            $this->channelRepository
        );
    }

    public function test_that_it_creates_connection_on_active_campaign(): void
    {
        $channel = $this->createChannel();
        $this->connectionCreateHandler->__invoke(new ConnectionCreate($channel->getId()));

        /** @var ChannelInterface|null $channel */
        $channel = $this->channelRepository->find($channel->getId());
        $this->assertEquals(1, $channel->getActiveCampaignId());
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
