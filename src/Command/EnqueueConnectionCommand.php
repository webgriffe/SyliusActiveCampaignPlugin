<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Command;

use InvalidArgumentException;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Stopwatch\Stopwatch;
use Webgriffe\SyliusActiveCampaignPlugin\Enqueuer\ConnectionEnqueuerInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Repository\ActiveCampaignResourceRepositoryInterface;

final class EnqueueConnectionCommand extends Command
{
    use LockableTrait;

    private const CHANNEL_ID_ARGUMENT_CODE = 'channel-id';

    public const ENQUEUE_CONNNECTION_COMMAND_STOPWATCH_NAME = 'enqueue-connection-command';

    public const ALL_OPTION_CODE = 'all';

    /** @psalm-suppress PropertyNotSetInConstructor */
    private SymfonyStyle $io;

    /** @param ActiveCampaignResourceRepositoryInterface<ChannelInterface> $channelRepository */
    public function __construct(
        private ActiveCampaignResourceRepositoryInterface $channelRepository,
        private ConnectionEnqueuerInterface $connectionEnqueuer,
        private ?string $name = null
    ) {
        parent::__construct($this->name);
    }

    protected function configure(): void
    {
        $this
            ->setHelp($this->getCommandHelp())
            ->addArgument(self::CHANNEL_ID_ARGUMENT_CODE, InputArgument::OPTIONAL, 'The identifier id of the channel to enqueue.')
            ->addOption(self::ALL_OPTION_CODE, 'a', InputOption::VALUE_NONE, 'If set, the command will enqueue all the channels.')
        ;
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        if ($input->getOption(self::ALL_OPTION_CODE) === true || $input->getArgument(self::CHANNEL_ID_ARGUMENT_CODE) !== null) {
            return;
        }

        $this->io->title('Enqueue Connection Command Interactive Wizard');
        $this->io->text([
            'If you prefer to not use this interactive wizard, provide the',
            'argument required by this command as follows:',
            '',
            ' $ php bin/console ' . (string) $this->name . ' channel-id',
            '',
            'Now we\'ll ask you for the value of the customer to enqueue.',
        ]);

        // Ask for the Channel ID if it's not defined
        /** @var mixed|null $channelId */
        $channelId = $input->getArgument(self::CHANNEL_ID_ARGUMENT_CODE);
        if (null === $channelId) {
            /** @var mixed $channelId */
            $channelId = $this->io->ask('Channel id', null, [$this, 'validateChannelId']);
            $input->setArgument(self::CHANNEL_ID_ARGUMENT_CODE, $channelId);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->lock()) {
            $this->io->error('The command is already running in another process.');

            return Command::FAILURE;
        }
        $stopwatch = new Stopwatch();
        $stopwatch->start(self::ENQUEUE_CONNNECTION_COMMAND_STOPWATCH_NAME);

        /** @var string|int $channelId */
        $channelId = $input->getArgument(self::CHANNEL_ID_ARGUMENT_CODE);
        $exportAll = (bool) $input->getOption(self::ALL_OPTION_CODE);

        $this->validateInputData($channelId, $exportAll);

        if ($exportAll) {
            $channelsToExport = $this->channelRepository->findAllToEnqueue();
        } else {
            $channel = $this->channelRepository->findOneToEnqueue($channelId);
            if ($channel === null) {
                throw new InvalidArgumentException(sprintf(
                    'Unable to find a Channel with id "%s".',
                    $channelId
                ));
            }
            $channelsToExport = [$channel];
        }
        if (count($channelsToExport) === 0) {
            $this->io->writeln('No channels founded to enqueue.');

            return Command::SUCCESS;
        }
        $progressBar = $this->getProgressBar($output, $channelsToExport);

        foreach ($channelsToExport as $channel) {
            if (!$channel instanceof ActiveCampaignAwareInterface) {
                continue;
            }
            $this->connectionEnqueuer->enqueue($channel);
            $progressBar->setMessage(sprintf('Channel "%s" enqueued!', (string) $channel->getId()), 'status');
            $progressBar->advance();
        }
        $progressBar->setMessage(sprintf('Finished to enqueue the %s channels 🎉', count($channelsToExport)), 'status');
        $progressBar->finish();

        $event = $stopwatch->stop(self::ENQUEUE_CONNNECTION_COMMAND_STOPWATCH_NAME);
        $this->io->comment(sprintf('Elapsed time: %.2f ms / Consumed memory: %.2f MB', $event->getDuration(), $event->getMemory() / (1024 ** 2)));

        $this->release();

        return Command::SUCCESS;
    }

    /**
     * @param string|int|null $customerId
     */
    private function validateInputData(mixed $customerId, bool $all): void
    {
        if ($all) {
            return;
        }
        $this->validateChannelId($customerId);
    }

    /**
     * @param string|int|null $channelId
     *
     * @return string|int
     */
    public function validateChannelId($channelId)
    {
        if ($channelId === null || $channelId === '') {
            throw new InvalidArgumentException('The Channel id can not be empty.');
        }

        return $channelId;
    }

    private function getCommandHelp(): string
    {
        return <<<'HELP'
            The <info>%command.name%</info> command enqueue all Sylius channels to export them on ActiveCampaign:

              <info>php %command.full_name%</info> <comment>channel-id</comment>

            By default the command enqueue only one channel. To enqueue all channels,
            add the <comment>--all</comment> option:

              <info>php %command.full_name%</info> <comment>--all</comment>

            If you omit the argument and the option, the command will ask you to
            provide the missing channel id:

              # command will ask you for the channel id
              <info>php %command.full_name%</info>
            HELP;
    }

    private function getProgressBar(OutputInterface $output, array $channelsToExport): ProgressBar
    {
        $progressBar = new ProgressBar($output, count($channelsToExport));
        $progressBar->setFormat(
            "<fg=white;bg=black> %status:-45s%</>\n%current%/%max% [%bar%] %percent:3s%%\n🏁  %estimated:-21s% %memory:21s%"
        );
        $progressBar->setBarCharacter('<fg=red>⚬</>');
        $progressBar->setEmptyBarCharacter('<fg=blue>⚬</>');
        $progressBar->setProgressCharacter('🚀');
        $progressBar->setRedrawFrequency(10);
        $progressBar->setMessage(sprintf('Starting the enqueue for %s channels...', count($channelsToExport)), 'status');
        $progressBar->start();

        return $progressBar;
    }
}
