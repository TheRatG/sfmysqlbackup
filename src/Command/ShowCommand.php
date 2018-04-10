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
use App\Helper\BackupFinder;
use Psr\Log\LoggerInterface;

class ShowCommand extends ContainerAwareCommand
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    protected function configure()
    {
        $this
            ->setName('show')
            ->setDescription('Show available backups')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->logger = $this->getContainer()->get('monolog.logger.show_command');
        $this->logger->debug(sprintf('"%s" command started', $this->getName()));

        $helper = $this->getContainer()->get(BackupFinder::class);

        $collection = $helper->find();

        $io = new SymfonyStyle($input, $output);
        $headers = ['Name', 'Date Time', 'Directory'];
        $rows = [];
        foreach ($collection->sort() as $key => $backup) {
            $rows[] = [
                basename($backup->getDirectory()),
                $backup->getDatetime()->format('Y-m-d H:i:s'),
                $backup->getDirectory(),
            ];
        }

        $io->table($headers, $rows);
    }
}
