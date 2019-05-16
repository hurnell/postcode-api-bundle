<?php


namespace Hurnell\PostcodeApiBundle\DependencyInjection;


use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('hurnell_postcode_api');
        $rootNode = $treeBuilder->root('hurnell_postcode_api');
        $rootNode
            ->children()
            ->scalarNode('api_key')
            ->isRequired()
            ->info('You\'re postcodeapi.nu api key')->end()
            ->end();

        return $treeBuilder;
    }
}
