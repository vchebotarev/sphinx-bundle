<?php

namespace Chebur\SphinxBundle\DependencyInjection;

use Chebur\SphinxBundle\Sphinx\Decorator\ConnectionDecorator;
use Chebur\SphinxBundle\Sphinx\Manager;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * @inheritdoc
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('chebur_sphinx');

        $rootNode
            ->children()
                ->arrayNode('connections')
                    ->requiresAtLeastOneElement()
                    ->addDefaultChildrenIfNoneSet(Manager::DEFAULT_NAME)
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('host')->defaultValue(ConnectionDecorator::DEFAULT_HOST)->end()
                            ->integerNode('port')->defaultValue(ConnectionDecorator::DEFAULT_PORT)->end()
                            ->integerNode('port_api')->defaultValue(ConnectionDecorator::DEFAULT_PORT_API)->end()
                            ->enumNode('driver')
                                ->values([
                                    ConnectionDecorator::DRIVER_MYSQLI,
                                    ConnectionDecorator::DRIVER_PDO,
                                ])
                                ->defaultValue(ConnectionDecorator::DRIVER_PDO)
                            ->end()
                            ->booleanNode('default')->defaultValue(false)->end()
                        ->end()
                    ->end()
                ->end()

                ->arrayNode('config')
                    ->children()
                        ->scalarNode('template')->defaultValue('%kernel.root_dir%/config/sphinx.conf.twig')->end()
                        ->scalarNode('destination')->defaultValue('%kernel.cache_dir%/sphinx/config.conf')->end()
                        ->arrayNode('searchd')
                            ->isRequired()
                            ->requiresAtLeastOneElement()
                            ->useAttributeAsKey('name')
                            ->prototype('variable')->end()
                        ->end()
                        ->arrayNode('sources')
                            ->isRequired()
                            ->requiresAtLeastOneElement()
                            ->useAttributeAsKey('name')
                            ->prototype('array')
                                ->requiresAtLeastOneElement()
                                ->useAttributeAsKey('name')
                                ->prototype('scalar')->end()
                            ->end()
                        ->end()
                        ->arrayNode('parameters')
                            ->defaultValue([])
                            ->useAttributeAsKey('name')
                            ->prototype('scalar')->end()
                        ->end()
                    ->end()
                ->end()

                ->arrayNode('commands')
                    ->children()
                        ->scalarNode('bin')->defaultValue(DIRECTORY_SEPARATOR == '/' ? '/usr/local/sphinx/bin' : 'c:\sphinx\bin')->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }

}
