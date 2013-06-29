<?php

namespace Sescandell\ContextRestrictorBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Config\Definition\Processor;


/**
 * @author Stéphane Escandell
 */
class SescandellContextRestrictorExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        // TODO: use XML
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, $configs);

        $container->getDefinition('context_restrictor.doctrine_listener')->addArgument($config['target_entity']);
        $container->getDefinition('context_restrictor.manager')->addMethodCall(
            'addRestriction',
            array(
                'target_entity' => $config['target_entity'],
                'field_name' => $config['field_name']
            )
        );
    }

    public function getAlias()
    {
        return 'context_restrictor';
    }
}
