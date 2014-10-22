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

        $config = $this->processConfiguration($this->getConfiguration($configs, $container), $configs);

        $container
            ->getDefinition('context_restrictor.doctrine_listener')
            ->addArgument($config['target_class']);

        $container
            ->getDefinition('context_restrictor.manager')
            ->setArguments(array(
                array(
                    'targetClass' => $config['target_class'],
                    'fieldName' => $config['field_name'],
                    'nullableMappings' => array_key_exists('nullable_mappings', $config) ? $config['nullable_mappings'] : array()
                ),
                $config['filter']['name'],
                $config['filter']['enabled']
            ));

        $container->setAlias('context_restrictor.volatile_storage', 'context_restrictor.storage.in_memory');
        $container->setAlias('context_restrictor.persistent_storage', 'context_restrictor.storage.session');
    }

    /**
     * @param array $config
     * @param ContainerBuilder $container
     * @return \Sescandell\ContextRestrictorBundle\DependencyInjection\Configuration
     */
    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        return new Configuration($this->getAlias());
    }

    public function getAlias()
    {
        return 'context_restrictor';
    }
}
