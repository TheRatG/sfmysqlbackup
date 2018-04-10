<?php

namespace App\Helper;

use App\Config\AppConfig;
use App\Model\Backup;
use App\Model\BackupCollection;
use Psr\Log\LoggerInterface;

class BackupFinder
{
    /**
     * @var AppConfig
     */
    protected $config;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(AppConfig $config, LoggerInterface $logger)
    {
        $this->config = $config;
        $this->logger = $logger;
    }

    public function getByDatetime(\DateTime $datetime)
    {
        $result = null;
        $collection = $this->findLocal();
        $result = $collection->getByDatetime($datetime);

        if (!$result) {
            $collection = $this->findRemote();
            $result = $collection->getByDatetime($datetime);
        }

        return $result;
    }

    /*
     * return BackupCollection
     */
    public function findAll()
    {
        $data = $this->findLocal();

        $result = $data->sort();

        return $result;
    }

    public function findLocal()
    {
        $dir = $this->config->getLocalDir();

        $result = new BackupCollection();
        foreach (glob($dir.'/*/metadata') as $filename) {
            try {
                $element = new Backup(dirname($filename), Backup::TYPE_LOCAL);
                $result->add($element);
            } catch (\InvalidArgumentException $e) {
                $this->logger->warning($e->getMessage());
            }
        }

        return $result;
    }

    public function findRemote()
    {
        $result = new BackupCollection();
        return $result;
    }
}
