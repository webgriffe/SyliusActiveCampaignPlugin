<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Command;

use DateTime;
use Psr\Log\LoggerInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Stopwatch\Stopwatch;
use Throwable;
use Webgriffe\SyliusActiveCampaignPlugin\Enqueuer\ContactEnqueuerInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Enqueuer\EcommerceCustomerEnqueuerInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Enqueuer\EcommerceOrderEnqueuerInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Contact\ContactListsSubscriber;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Contact\ContactTagsAdder;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ChannelActiveCampaignAwareInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\CustomerActiveCampaignAwareInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Repository\ActiveCampaignOrderRepositoryInterface;
use Webmozart\Assert\Assert;

final class EnqueueEcommerceAbandonedCartCommand extends Command
{
    use LockableTrait;

    public const ENQUEUE_ECOMMERCE_ABANDONED_CART_COMMAND_STOPWATCH_NAME = 'enqueue-ecommerce-abandoned-cart-command';

    /** @psalm-suppress PropertyNotSetInConstructor */
    private SymfonyStyle $io;

    public function __construct(
        private ActiveCampaignOrderRepositoryInterface $orderRepository,
        private EcommerceOrderEnqueuerInterface $ecommerceOrderEnqueuer,
        private LoggerInterface $logger,
        private ContactEnqueuerInterface $contactEnqueuer,
        private EcommerceCustomerEnqueuerInterface $ecommerceCustomerEnqueuer,
        private MessageBusInterface $messageBus,
        private string $cartBecomesAbandonedPeriod,
        private ?string $name = null,
    ) {
        parent::__construct($this->name);
    }

    protected function configure(): void
    {
        $this
            ->setHelp($this->getCommandHelp())
        ;
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->lock()) {
            $this->io->error('The command is already running in another process.');

            return Command::FAILURE;
        }
        $stopwatch = new Stopwatch();
        $stopwatch->start(self::ENQUEUE_ECOMMERCE_ABANDONED_CART_COMMAND_STOPWATCH_NAME);

        $abandonedCarts = $this->orderRepository->findNewCartsNotModifiedSince(new DateTime('-' . $this->cartBecomesAbandonedPeriod));
        if (count($abandonedCarts) === 0) {
            $this->io->writeln('No new carts founded to enqueue.');

            return Command::SUCCESS;
        }

        $progressBar = $this->getProgressBar($output, $abandonedCarts);

        foreach ($abandonedCarts as $abandonedCart) {
            if (!$abandonedCart instanceof ActiveCampaignAwareInterface) {
                continue;
            }
            Assert::null($abandonedCart->getActiveCampaignId());
            $customer = $abandonedCart->getCustomer();
            Assert::isInstanceOf($customer, CustomerInterface::class);
            if (!$customer instanceof CustomerActiveCampaignAwareInterface) {
                continue;
            }
            /** @var string|int|null $customerId */
            $customerId = $customer->getId();
            Assert::notNull($customerId);
            $channel = $abandonedCart->getChannel();
            Assert::isInstanceOf($channel, ChannelInterface::class);
            if (!$channel instanceof ChannelActiveCampaignAwareInterface) {
                continue;
            }

            try {
                $this->contactEnqueuer->enqueue($customer);
                $this->ecommerceCustomerEnqueuer->enqueue($customer, $channel);
                $this->messageBus->dispatch(new ContactTagsAdder($customerId));
                $this->messageBus->dispatch(new ContactListsSubscriber($customerId));
                $this->ecommerceOrderEnqueuer->enqueue($abandonedCart, true);
            } catch (Throwable $throwable) {
                $this->logger->error($throwable->getMessage(), $throwable->getTrace());

                continue;
            }

            $progressBar->setMessage(sprintf('Abandoned cart "%s" enqueued!', (string) $abandonedCart->getId()), 'status');
            $progressBar->advance();
        }
        $progressBar->setMessage(sprintf('Finished to enqueue the %s abandoned carts 🎉', count($abandonedCarts)), 'status');
        $progressBar->finish();

        $event = $stopwatch->stop(self::ENQUEUE_ECOMMERCE_ABANDONED_CART_COMMAND_STOPWATCH_NAME);
        $this->io->comment(sprintf('Elapsed time: %.2f ms / Consumed memory: %.2f MB', $event->getDuration(), $event->getMemory() / (1024 ** 2)));

        $this->release();

        return Command::SUCCESS;
    }

    private function getCommandHelp(): string
    {
        return <<<'HELP'
            The <info>%command.name%</info> command enqueue all abandoned Sylius carts to export them on ActiveCampaign
            and making start all triggers about this event:

              <info>php %command.full_name%</info>
            HELP;
    }

    protected function getProgressBar(OutputInterface $output, array $abandonedCarts): ProgressBar
    {
        $progressBar = new ProgressBar($output, count($abandonedCarts));
        $progressBar->setFormat(
            "<fg=white;bg=black> %status:-45s%</>\n%current%/%max% [%bar%] %percent:3s%%\n🏁  %estimated:-21s% %memory:21s%",
        );
        $progressBar->setBarCharacter('<fg=red>⚬</>');
        $progressBar->setEmptyBarCharacter('<fg=blue>⚬</>');
        $progressBar->setProgressCharacter('🚀');
        $progressBar->setRedrawFrequency(10);
        $progressBar->setMessage(sprintf('Starting the enqueue for %s abandoned carts...', count($abandonedCarts)), 'status');
        $progressBar->start();

        return $progressBar;
    }
}
