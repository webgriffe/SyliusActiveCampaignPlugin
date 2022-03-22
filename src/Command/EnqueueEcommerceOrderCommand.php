<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Command;

use InvalidArgumentException;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Stopwatch\Stopwatch;
use Webgriffe\SyliusActiveCampaignPlugin\Message\EcommerceOrder\EcommerceOrderCreate;
use Webgriffe\SyliusActiveCampaignPlugin\Repository\ActiveCampaignAwareRepositoryInterface;
use Webmozart\Assert\Assert;

final class EnqueueEcommerceOrderCommand extends Command
{
    use LockableTrait;

    private const ORDER_ID_ARGUMENT_CODE = 'order-id';

    public const ENQUEUE_ECOMMERCE_ORDER_COMMAND_STOPWATCH_NAME = 'enqueue-ecommerce-order-command';

    public const ALL_OPTION_CODE = 'all';

    /** @psalm-suppress PropertyNotSetInConstructor */
    private SymfonyStyle $io;

    /** @param ActiveCampaignAwareRepositoryInterface<OrderInterface> $orderRepository */
    public function __construct(
        private ActiveCampaignAwareRepositoryInterface $orderRepository,
        private MessageBusInterface $messageBus,
        private ?string $name = null
    ) {
        parent::__construct($this->name);
    }

    protected function configure(): void
    {
        $this
            ->setHelp($this->getCommandHelp())
            ->addArgument(self::ORDER_ID_ARGUMENT_CODE, InputArgument::OPTIONAL, 'The identifier id of the order to enqueue.')
            ->addOption(self::ALL_OPTION_CODE, 'a', InputOption::VALUE_NONE, 'If set, the command will enqueue all the orders without an ActiveCampaign\'s id.')
        ;
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        if ($input->getOption(self::ALL_OPTION_CODE) === true || $input->getArgument(self::ORDER_ID_ARGUMENT_CODE) !== null) {
            return;
        }

        $this->io->title('Enqueue Ecommerce Order Command Interactive Wizard');
        $this->io->text([
            'If you prefer to not use this interactive wizard, provide the',
            'argument required by this command as follows:',
            '',
            ' $ php bin/console ' . (string) $this->name . ' order-id',
            '',
            'Now we\'ll ask you for the value of the order to enqueue.',
        ]);

        // Ask for the Order ID if it's not defined
        /** @var mixed|null $orderId */
        $orderId = $input->getArgument(self::ORDER_ID_ARGUMENT_CODE);
        if (null === $orderId) {
            /** @var mixed $orderId */
            $orderId = $this->io->ask('Order id', null, [$this, 'validateOrderId']);
            $input->setArgument(self::ORDER_ID_ARGUMENT_CODE, $orderId);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->lock()) {
            $this->io->error('The command is already running in another process.');

            return Command::FAILURE;
        }
        $stopwatch = new Stopwatch();
        $stopwatch->start(self::ENQUEUE_ECOMMERCE_ORDER_COMMAND_STOPWATCH_NAME);

        /** @var string|int $orderId */
        $orderId = $input->getArgument(self::ORDER_ID_ARGUMENT_CODE);
        $exportAll = (bool) $input->getOption(self::ALL_OPTION_CODE);

        $this->validateInputData($orderId, $exportAll);

        if ($exportAll) {
            $ordersToExport = $this->orderRepository->findAllToEnqueue();
        } else {
            $order = $this->orderRepository->findOneToEnqueue($orderId);
            if ($order === null) {
                throw new InvalidArgumentException(sprintf(
                    'Unable to find an Order with id "%s".',
                    $orderId
                ));
            }
            $ordersToExport = [$order];
        }

        $progressBar = new ProgressBar($output, count($ordersToExport));
        $progressBar->setFormat(
            "<fg=white;bg=black> %status:-45s%</>\n%current%/%max% [%bar%] %percent:3s%%\n🏁  %estimated:-21s% %memory:21s%"
        );
        $progressBar->setBarCharacter('<fg=red>⚬</>');
        $progressBar->setEmptyBarCharacter('<fg=blue>⚬</>');
        $progressBar->setProgressCharacter('🚀');
        $progressBar->setRedrawFrequency(10);
        $progressBar->setMessage(sprintf('Starting the enqueue for %s orders...', count($ordersToExport)), 'status');
        $progressBar->start();

        foreach ($ordersToExport as $order) {
            /** @var string|int|null $orderId */
            $orderId = $order->getId();
            Assert::notNull($orderId);
            $this->messageBus->dispatch(new EcommerceOrderCreate($orderId, false));
            $progressBar->setMessage(sprintf('Order "%s" enqueued!', (string) $order->getId()), 'status');
            $progressBar->advance();
        }
        $progressBar->setMessage(sprintf('Finished to enqueue the %s orders 🎉', count($ordersToExport)), 'status');
        $progressBar->finish();

        $event = $stopwatch->stop(self::ENQUEUE_ECOMMERCE_ORDER_COMMAND_STOPWATCH_NAME);
        $this->io->comment(sprintf('Elapsed time: %.2f ms / Consumed memory: %.2f MB', $event->getDuration(), $event->getMemory() / (1024 ** 2)));

        $this->release();

        return Command::SUCCESS;
    }

    /**
     * @param string|int|null $orderId
     */
    private function validateInputData(mixed $orderId, bool $all): void
    {
        if ($all) {
            return;
        }
        $this->validateOrderId($orderId);
    }

    /**
     * @param string|int|null $orderId
     *
     * @return string|int
     */
    public function validateOrderId($orderId)
    {
        if ($orderId === null || $orderId === '') {
            throw new InvalidArgumentException('The Order id can not be empty.');
        }

        return $orderId;
    }

    private function getCommandHelp(): string
    {
        return <<<'HELP'
            The <info>%command.name%</info> command enqueue all Sylius orders without an ActiveCampaign's id to export them on ActiveCampaign:

              <info>php %command.full_name%</info> <comment>order-id</comment>

            By default the command enqueue only one order. To enqueue all orders,
            add the <comment>--all</comment> option:

              <info>php %command.full_name%</info> <comment>--all</comment>

            If you omit the argument and the option, the command will ask you to
            provide the missing order id:

              # command will ask you for the order id
              <info>php %command.full_name%</info>
            HELP;
    }
}
