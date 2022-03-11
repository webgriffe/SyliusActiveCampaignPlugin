<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Command;

use InvalidArgumentException;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
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
use Webgriffe\SyliusActiveCampaignPlugin\Message\ContactCreate;
use Webmozart\Assert\Assert;

final class EnqueueContactCommand extends Command
{
    use LockableTrait;

    private const CUSTOMER_ID_ARGUMENT_CODE = 'customer-id';

    public const ENQUEUE_CONTACT_COMMAND_STOPWATCH_NAME = 'enqueue-contact-command';

    public const ALL_OPTION_CODE = 'all';

    /** @psalm-suppress PropertyNotSetInConstructor */
    private SymfonyStyle $io;

    public function __construct(
        private CustomerRepositoryInterface $customerRepository,
        private MessageBusInterface $messageBus,
        private ?string $name = null
    ) {
        parent::__construct($this->name);
    }

    protected function configure(): void
    {
        $this
            ->setHelp($this->getCommandHelp())
            ->addArgument(self::CUSTOMER_ID_ARGUMENT_CODE, InputArgument::OPTIONAL, 'The identifier id of the customer to enqueue.')
            ->addOption(self::ALL_OPTION_CODE, 'a', InputOption::VALUE_NONE, 'If set, the command will enqueue all the customers without an ActiveCampaign\'s id.')
        ;
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        if ($input->getOption(self::ALL_OPTION_CODE) === true || $input->getArgument(self::CUSTOMER_ID_ARGUMENT_CODE) !== null) {
            return;
        }

        $this->io->title('Enqueue Contact Command Interactive Wizard');
        $this->io->text([
            'If you prefer to not use this interactive wizard, provide the',
            'argument required by this command as follows:',
            '',
            ' $ php bin/console ' . (string) $this->name . ' customer-id',
            '',
            'Now we\'ll ask you for the value of the customer to enqueue.',
        ]);

        // Ask for the Customer ID if it's not defined
        /** @var mixed|null $customerId */
        $customerId = $input->getArgument(self::CUSTOMER_ID_ARGUMENT_CODE);
        if (null === $customerId) {
            /** @var mixed $customerId */
            $customerId = $this->io->ask('Customer id', null, [$this, 'validateCustomerId']);
            $input->setArgument(self::CUSTOMER_ID_ARGUMENT_CODE, $customerId);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->lock()) {
            $this->io->error('The command is already running in another process.');

            return Command::FAILURE;
        }
        $stopwatch = new Stopwatch();
        $stopwatch->start(self::ENQUEUE_CONTACT_COMMAND_STOPWATCH_NAME);

        /** @var string|int $customerId */
        $customerId = $input->getArgument(self::CUSTOMER_ID_ARGUMENT_CODE);
        /** @var bool $exportAll */
        $exportAll = $input->getOption(self::ALL_OPTION_CODE);

        $this->validateInputData($customerId, $exportAll);

        if ($exportAll) {
            $customersToExport = $this->customerRepository->findAll();
        } else {
            /** @var CustomerInterface|null $customer */
            $customer = $this->customerRepository->find($customerId);
            if ($customer === null) {
                throw new InvalidArgumentException(sprintf(
                    'Unable to find a customer with id "%s".',
                    $customerId
                ));
            }
            $customersToExport = [$customer];
        }

        $progressBar = new ProgressBar($output, count($customersToExport));
        $progressBar->setFormat(
            "<fg=white;bg=black> %status:-45s%</>\n%current%/%max% [%bar%] %percent:3s%%\n🏁  %estimated:-21s% %memory:21s%"
        );
        $progressBar->setBarCharacter('<fg=red>⚬</>');
        $progressBar->setEmptyBarCharacter('<fg=blue>⚬</>');
        $progressBar->setProgressCharacter('🚀');
        $progressBar->setRedrawFrequency(10);
        $progressBar->setMessage(sprintf('Starting the enqueue for %s customers...', count($customersToExport)), 'status');
        $progressBar->start();

        /** @var CustomerInterface $customer */
        foreach ($customersToExport as $customer) {
            /** @var string|int|null $customerId */
            $customerId = $customer->getId();
            Assert::notNull($customerId);
            $this->messageBus->dispatch(new ContactCreate($customerId));
            $progressBar->setMessage(sprintf('Customer "%s" enqueued!', (string) $customer->getId()), 'status');
            $progressBar->advance();
        }
        $progressBar->setMessage(sprintf('Finished to enqueue the %s customers 🎉', count($customersToExport)), 'status');
        $progressBar->finish();

        $event = $stopwatch->stop(self::ENQUEUE_CONTACT_COMMAND_STOPWATCH_NAME);
        $this->io->comment(sprintf('Elapsed time: %.2f ms / Consumed memory: %.2f MB', $event->getDuration(), $event->getMemory() / (1024 ** 2)));

        $this->release();

        return Command::SUCCESS;
    }

    /**
     * @param string|int|null $customerId
     */
    private function validateInputData(mixed $customerId, mixed $all): void
    {
        if ((bool) $all) {
            return;
        }
        $this->validateCustomerId($customerId);
    }

    /**
     * @param string|int|null $customerId
     *
     * @return string|int
     */
    public function validateCustomerId($customerId)
    {
        if ($customerId === null || $customerId === '') {
            throw new InvalidArgumentException('The customer id can not be empty.');
        }

        return $customerId;
    }

    private function getCommandHelp(): string
    {
        return <<<'HELP'
            The <info>%command.name%</info> command enqueue all Sylius customers without an ActiveCampaign's id to export them on ActiveCampaign:

              <info>php %command.full_name%</info> <comment>customer-id</comment>

            By default the command enqueue only one customer. To enqueue all customers,
            add the <comment>--all</comment> option:

              <info>php %command.full_name%</info> <comment>--all</comment>

            If you omit the argument and the option, the command will ask you to
            provide the missing customer id:

              # command will ask you for the customer id
              <info>php %command.full_name%</info>
            HELP;
    }
}
