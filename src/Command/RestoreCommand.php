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
use App\Helper\BackupFinder;

class RestoreCommand extends ContainerAwareCommand
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    protected function configure()
    {
        $this
            ->setName('restore')
            ->setDescription('Load dump')
            ->addArgument('name', InputArgument::REQUIRED, 'Restore backup name')
            ->addOption('overwrite-tables-disable', null, InputOption::VALUE_NONE, 'Drop tables if they already exist')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Execute the command as a dry run.')
            ->addOption('overwrite-database', null, InputOption::VALUE_NONE, 'Overwrite database')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dryRun = $input->getOption('dry-run');

        $this->logger = $this->getContainer()->get('monolog.logger.restore_command');
        $this->logger->debug(sprintf('"%s" command started', $this->getName()));

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

    protected function buildCmd(InputInterface $input) {
        $datetime = \DateTime::createFromFormat(\App\Model\Backup::DT_FORMAT, $input->getArgument('name'));
        if (!$datetime) {
            throw new \RuntimeException(sprintf('Invalid name argument "%s"', $input->getArgument('name')));
        }
        $subdir = $datetime->format(\App\Model\Backup::DT_FORMAT);
        $helper = $this->getContainer()->get(BackupFinder::class);
        $backup = $helper->getByDatetime($datetime);

        if (!$backup) {
            $this->logger->info(sprintf('Backup "%s" not found', $subdir));

            return;
        }

        $config = $this->getContainer()->get(\App\Config\AppConfig::class);
        $cmd = [
            'myloader',
            '--directory=' . $config->getLocalDir() . '/' . $subdir,
            '--compress-protocol',
            '--verbose=2',
        ];

        $connectionParams = $config->getConnectionParams();
        if (!$input->getOption('overwrite-database')) {
            $connectionParams['database'] = $connectionParams['database'] . '_' . $subdir;
        } else {
            //todo: overwrite confirm
        }

        $cmd = array_merge($cmd, $connectionParams);

        if (!$input->getOption('overwrite-tables-disable')) {
            $cmd[] = '--overwrite-tables';
        }

        return $cmd;
    }
}