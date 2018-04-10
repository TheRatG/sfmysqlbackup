<?php
namespace App\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Yaml\Yaml;

class InitCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('init')
            ->setDescription('Init configuration')
            ->addOption(
                'local-dir',
                null,
                InputOption::VALUE_REQUIRED,
                'Local dir'
            )
            ->addOption(
                'database-url',
                null,
                InputOption::VALUE_REQUIRED,
                'Database URL',
                'mysql://homestead:secret@localhost:3306/test'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Welcome to configuration entertainment');

        $dir = $this->getContainer()->get('kernel')->geSfMysqlBackupDir();

        $configFilename = $dir . '/config.yaml';
        if (file_exists($configFilename) && !$io->confirm('Config file already exists, override?', false)) {
            $io->text(sprintf('Config file already exists "%s"', $configFilename));

            return;
        }

        $config = ['sfmysqlbackup' => []];

        $localDir = $input->getOption('local-dir');
        if (!$localDir) {
            $localDir = $io->ask('Enter local dir path:', $dir . '/backups');
        }

        $databaseUrl = $input->getOption('database-url');
        if (!$databaseUrl) {
            $databaseUrl = $io->ask('Enter database url:', 'mysql://homestead:secret@localhost:3306');
        }

        $config['sfmysqlbackup'] = [
            'local_dir' => $localDir,
            'database_url' => $databaseUrl,
            'remote' => null,
        ];

        try {
            $configuration = new \App\DependencyInjection\Configuration();
            $processor = new \Symfony\Component\Config\Definition\Processor();
            $processor->processConfiguration($configuration, $config);
        } catch (\Exception $ex) {
            $io->error($ex->getMessage());
        }

        $yaml = Yaml::dump($config);
        if (!file_exists(dirname($configFilename))) {
            mkdir(dirname($configFilename));
        }
        file_put_contents($configFilename, $yaml);
        $io->text(sprintf('Config file created "%s"', $configFilename));
    }
}
