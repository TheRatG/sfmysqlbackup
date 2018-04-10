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

    public function getByName($name)
    {
        $result = null;
        $collection = $this->find();
        $result = $collection->getByName($name);

        return $result;
    }

    public function getByDatetime(\DateTime $datetime)
    {
        $result = null;
        $collection = $this->find();
        $result = $collection->getByDatetime($datetime);

        return $result;
    }

    /*
     * return BackupCollection
     */
    public function find()
    {
        $dir = $this->config->getLocalDir();

        $result = new BackupCollection();
        foreach (glob($dir.'/*/metadata') as $filename) {
            try {
                $element = new Backup(dirname($filename));
                $result->add($element);
            } catch (\InvalidArgumentException $e) {
                $this->logger->warning($e->getMessage());
            }
        }

        return $result->sort();
    }
}
