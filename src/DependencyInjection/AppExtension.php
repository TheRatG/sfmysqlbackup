<?php
namespace App\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class AppExtension extends Extension
{
    const ALIAS = 'sfmysqlbackup';

    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $configs = $this->processConfiguration($configuration, $configs);
    }

    public function getAlias()
    {
        return self::ALIAS;
    }
}
