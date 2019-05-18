<?php


namespace Hurnell\PostcodeApiBundle\DependencyInjection;


use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{

    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('hurnell_postcode_api');
        if (method_exists($treeBuilder, 'getRootNode')) {
            $rootNode = $treeBuilder->getRootNode();
        } else {
            $rootNode = $treeBuilder->root('hurnell_postcode_api');
        }
        $rootNode
            ->children()
            ->scalarNode('api_key')
            ->isRequired()
            ->info('You\'re postcodeapi.nu api key')->end()
            ->end();

        return $treeBuilder;
    }
}
