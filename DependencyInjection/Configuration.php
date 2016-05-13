<?php

namespace AECF\TranslatorToolBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('translator_tool');

        $rootNode
            ->children()
                ->arrayNode('enabled_locales')
                    ->addDefaultsIfNotSet(array('%locale%'))
                ->end()
                ->arrayNode('auto_create_missing')
                    ->children()
                        ->booleanNode('enabled')
                            ->defaultTrue()
                        ->end()
                        ->enumNode('format')
                            ->values(array('yml', 'xlf', 'php'))
                            ->defaultValue('yml')
                        ->end()
                    ->end()
                ->end() // auto_create_missing
            ->end()
        ;

        return $treeBuilder;
    }
}
