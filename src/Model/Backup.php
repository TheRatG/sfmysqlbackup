<?php

namespace App\Model;

class Backup
{
    const DT_FORMAT = 'Ymd_Hi';

    /**
     * @var string
     */
    protected $directory;

    public function __construct($directory)
    {
        $this->setDirectory($directory);
    }

    public function getDatetime()
    {
        $result = \DateTime::createFromFormat(self::DT_FORMAT, $this->getName());

        return $result;
    }

    public function getName()
    {
        return basename($this->getDirectory());
    }

    /**
     * @return string
     */
    public function getDirectory()
    {
        return $this->directory;
    }

    /**
     * @param string $directory
     *
     * @return self
     */
    public function setDirectory($directory)
    {
        $this->directory = $directory;

        if (!$this->getDatetime() instanceof \DateTime) {
            throw new \InvalidArgumentException(sprintf('Invalid direcotry name "%s"', $directory));
        }

        return $this;
    }
}
