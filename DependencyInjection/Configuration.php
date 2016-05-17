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
                ->booleanNode('live_edit')
                    ->defaultFalse()
                ->end() // live_edit
                ->arrayNode('enabled_locales')
                    ->prototype('scalar')->end()
                    ->defaultValue(array('%locale%'))
                ->end() // enabled_locales
                ->arrayNode('auto_create_missing')
                    ->children()
                        ->booleanNode('enabled')
                            ->defaultTrue()
                        ->end()
                        ->arrayNode('formats')
                            ->prototype('scalar')
                            ->defaultValue(array('yml'))
                        ->end()
                    ->end()
                ->end() // auto_create_missing
            ->end()
        ;

        return $treeBuilder;
    }
}
