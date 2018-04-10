<?php

namespace App\Model;

class Backup 
{
    const DT_FORMAT = 'Ymd_His';

    const TYPE_LOCAL = 'local';
    const TYPE_REMOTE = 'remote';

    /**
     * @var string
     */
    protected $directory;

    /**
     * @var string
     */
    protected $type;

    public function __construct($directory, $type)
    {
        $this->setDirectory($directory);
        $this->setType($type);
    }

    public function getDatetime()
    {
        $directory = basename($this->getDirectory());
        $result = \DateTime::createFromFormat(self::DT_FORMAT, $directory);

        return $result;
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

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return self
     */
    public function setType($type)
    {
        if (!in_array($type, [self::TYPE_LOCAL, self::TYPE_REMOTE])) {
            throw new \InvalidArgumentExceptio(sprintf('Unknown type "%s', $type));
        }

        $this->type = $type;

        return $this;
    }

    public function getTypeOrderValue() {
        $map = [
            self::TYPE_LOCAL => 0,
            self::TYPE_REMOTE => 1,
        ];

        return $map[$this->getType()];
    }
}