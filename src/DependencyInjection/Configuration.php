<?php
namespace App\DependencyInjection;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root(AppExtension::ALIAS);

        $rootNode
            ->children()
                ->scalarNode('local_dir')
                    ->info('Local backups directory')
                    ->defaultValue('~/.sfmysqlbackup/backups')
                ->end()
                ->scalarNode('remote_url')
                    ->info('Remote url for "scp" unix command')
                    ->defaultValue(null)
                ->end()
                ->scalarNode('database_url')
                    ->info('Database URL')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
