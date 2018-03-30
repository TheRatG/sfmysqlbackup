<?php

namespace App\Config;

class AppConfig 
{
    /*
     * @var array
     */
    protected $config;

    public function __construct(array $config = []) {
        $this->config = $config;
    }
}