<?php
namespace App\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Process\Process;
use Psr\Log\LoggerInterface;

class CreateCommand extends ContainerAwareCommand
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    protected function configure()
    {
        $this
            ->setName('create')
            ->setDescription('Create dump')
            ->addOption('regex', 'r', InputOption::VALUE_REQUIRED, 'Once can use --regex functionality')
            ->addOption(
                'no-locks',
                null,
                InputOption::VALUE_NONE,
                'Do not execute the temporary shared read lock. WARNING: This will cause inconsistent backups'
            )
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Execute the command as a dry run.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dryRun = $input->getOption('dry-run');

        $this->logger = $this->getContainer()->get('monolog.logger.create_command');
        $this->logger->debug(sprintf('"%s" command started', $this->getName()), ['dry-run' => $dryRun]);

        $cmd = $this->buildCmd($input);
        $process = new Process($cmd);
        $timeout = 3600;
        $process->setTimeout($timeout);

        $this->logger->info(
            sprintf(
                'Run cmd: "%s"',
                preg_replace('/--password=(\\w+)/', '--password=*****', $process->getCommandLine())
            ),
            ['timeout' => $timeout]
        );

        if (!$dryRun) {
            $logger = $this->logger;
            $process->mustRun(function ($type, $buffer) use ($logger) {
                $logger->info($buffer);
            });
        }

        $this->logger->debug(sprintf('"%s" command finished', $this->getName()));
    }

    protected function buildCmd(InputInterface $input)
    {
        $config = $this->getContainer()->get(\App\Config\AppConfig::class);

        if (!file_exists($config->getLocalDir())) {
            mkdir($config->getLocalDir());
        }

        $datetime = new \DateTime();
        $cmd = [
            'mydumper',
            '--outputdir='
            . $config->getLocalDir()
            . '/'
            . $datetime->format(\App\Model\Backup::DT_FORMAT),
            '--compress',
            '--less-locking',
            '--verbose=2',
        ];

        $cmd = array_merge($cmd, $config->getConnectionParams());

        $cmd[] = sprintf('--logfile=%s/dump.log', $this->getContainer()->get('kernel')->getLogDir());

        if ($regex = $input->getOption('regex')) {
            $cmd[] = sprintf('--regex=%s', $regex);
        }

        if ($input->getOption('no-locks')) {
            $cmd[] = '--no-locks';
        }

        return $cmd;
    }
}
