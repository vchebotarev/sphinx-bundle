<?php

namespace Chebur\SphinxBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class CheburSphinxExtension extends Extension
{
    /**
     * @inheritdoc
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $container->setParameter('chebur_sphinx_config', $config);

        //Filling in Registry with managers
        $names       = [];
        $nameDefault = null;

        $loggerRef = new Reference('chebur.sphinx.profiler.logger');
        foreach($config['connections'] as $connectionName => $val) {
            $container->register('chebur.sphinx.connection.'.$connectionName, $container->getParameter('chebur.sphinx.connection.class'))
                ->addArgument($connectionName)
                ->addArgument($loggerRef)
                ->addArgument($val['driver'])
                ->addArgument($val['host'])
                ->addArgument($val['port'])
                ->addArgument($val['port_api'])
            ;

            $container->register('chebur.sphinx.manager.'.$connectionName, $container->getParameter('chebur.sphinx.manager.class'))
                ->addArgument(new Reference('chebur.sphinx.connection.' . $connectionName))
                ->setPublic(true)
            ;

            $names[] = $connectionName;
            if ($val['default']) {
                $nameDefault = $connectionName;
            }
        }

        if (!$nameDefault) {
            $nameDefault = $names[array_keys($names)[0]];
        }
        $container->setAlias('chebur.sphinx.manager', 'chebur.sphinx.manager.'.$nameDefault);

        $container->register('chebur.sphinx', $container->getParameter('chebur.sphinx.registry.class'))
            ->addArgument(new Reference('service_container'))
            ->addArgument($names)
            ->addArgument($nameDefault)
        ;
    }

}
