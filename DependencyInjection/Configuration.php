<?php

/**
 * This file is part of the zumo/zumokit-bundle package.
 *
 * (c) DLabs / Zumo 2019
 * Author Vladimir Strackovski <vladimir.strackovski@dlabs.si>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zumo\ZumokitBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\RotatingFileHandler;

/**
 * Class Configuration
 *
 * @package      Zumo\ZumokitBundle\DependencyInjection
 * @author       Vladimir Strackovski <vladimir.strackovski@dlabs.si>
 * @copyright    2018 DLabs (https://www.dlabs.si)
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('zumokit');

        if (method_exists($treeBuilder, 'getRootNode')) {
            $rootNode = $treeBuilder->getRootNode();
        } else {
            // BC layer for symfony/config 4.1 and older
            $rootNode = $treeBuilder->root('sensio_framework_extra');
        }

        $rootNode
            ->children()
            ->scalarNode('app_id')
            ->info('The ID of the app, available in the admin panel.')
            ->cannotBeEmpty()
            ->end()
            ->scalarNode('api_key')
            ->info('The API key, available in the admin panel.')
            ->cannotBeEmpty()
            ->end()
            ->scalarNode('app_name')
            ->info('The app name.')
            ->cannotBeEmpty()
            ->end()
            ->scalarNode('api_url')
            ->info('The base URI of the ZumoKit API instance.')
            ->cannotBeEmpty()
            ->end()
            ->end()
        ;

        $this->addUserRegistration($rootNode);
        $this->addSecurity($rootNode);
        $this->addMetadata($rootNode);
        $this->addDomains($rootNode);
        $this->addLogDestinations($rootNode);

        return $treeBuilder;
    }

    /**
     * @param ArrayNodeDefinition $rootNode
     */
    public function addSecurity(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
            ->arrayNode('security')
            ->addDefaultsIfNotSet()
            ->children()
            ->booleanNode('verify_ssl')
            ->defaultTrue()
            ->end()
            ->scalarNode('user_class')
            ->defaultValue('App\Entity\User')
            ->cannotBeEmpty()
            ->end()
            ->scalarNode('login_event')
            ->defaultValue('...')
            ->cannotBeEmpty()
            ->end()
            ->scalarNode('repository_class')
            ->defaultValue('App\Repository\UserRepository')
            ->cannotBeEmpty()
            ->end()
            ->arrayNode('jwt')
            ->addDefaultsIfNotSet()
            ->children()
            ->scalarNode('public_key')
            ->defaultValue('...')
            ->cannotBeEmpty()
            ->end()
            ->scalarNode('private_key')
            ->defaultValue('...')
            ->cannotBeEmpty()
            ->end()
            ->scalarNode('passphrase')
            ->defaultValue('...')
            ->cannotBeEmpty()
            ->end()
            ->scalarNode('shared_key')
            ->defaultValue('...')
            ->cannotBeEmpty()
            ->end()
            ->scalarNode('shared_secret')
            ->defaultValue('...')
            ->cannotBeEmpty()
            ->end()
            ->scalarNode('well_known_url')
            ->defaultValue('...')
            ->cannotBeEmpty()
            ->end()
            ->scalarNode('keyset')
            ->defaultValue('...')
            ->cannotBeEmpty()
            ->end()
            ->end()
            ->end()
            ->end()
            ->end()
            ->end()
            ->end()
        ;
    }

    /**
     * @param ArrayNodeDefinition $rootNode
     */
    public function addUserRegistration(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
            ->arrayNode('user_registration')
            ->info('')
            ->addDefaultsIfNotSet()
            ->children()
            ->booleanNode('enable')
            ->defaultFalse()
            ->end()
            ->scalarNode('event')
            ->defaultValue('The event FQ class name to handle.')
            ->cannotBeEmpty()
            ->end()
            ->end()
            ->end()
            ->end()
        ;
    }

    /**
     * @param ArrayNodeDefinition $rootNode
     */
    public function addMetadata(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
            ->arrayNode('metadata')
            ->addDefaultsIfNotSet()
            ->children()
            ->booleanNode('enable')
            ->defaultTrue()
            ->end()
            ->scalarNode('id')
            ->defaultValue('')
            ->cannotBeEmpty()
            ->end()
            ->scalarNode('secret')
            ->defaultValue('asd')
            ->cannotBeEmpty()
            ->end()
            ->scalarNode('endpoint_url')
            ->defaultValue('/m/api/machine/ID/metadata.enc')
            ->cannotBeEmpty()
            ->end()
            ->scalarNode('root')
            ->defaultValue('/var/')
            ->cannotBeEmpty()
            ->end()
            ->end()
            ->end()
            ->end()
        ;
    }

    /**
     * @param ArrayNodeDefinition $rootNode
     */
    public function addDomains(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->fixXmlConfig('domain')
            ->children()
            ->scalarNode('primary_domain')
            ->defaultValue('api.staging.zumopay.com')
            ->cannotBeEmpty()
            ->end()
            ->arrayNode('domains')
            ->addDefaultChildrenIfNoneSet()
            ->info('Authorized domain names.')
            ->scalarPrototype()->cannotBeEmpty()->end()
            ->end()
            ->end()
        ;
    }

    /**
     * @param ArrayNodeDefinition $rootNode
     */
    public function addLogDestinations(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->fixXmlConfig('logging')
            ->children()
            ->arrayNode('logging')
            ->info('')
            ->addDefaultChildrenIfNoneSet('zumokit_server')
            ->useAttributeAsKey('name')
            ->arrayPrototype()
            ->children()
            ->booleanNode('enable')
            ->defaultTrue()
            ->end()
            ->enumNode('level')
            ->values(array('info', 'notice', 'warning', 'critical', 'alert'))
            ->defaultValue('info')
            ->end()
            ->scalarNode('handler')
            ->defaultValue(RotatingFileHandler::class)
            ->end()
            ->scalarNode('formatter')
            ->defaultValue(JsonFormatter::class)
            ->end()
            ->booleanNode('anonimize')
            ->defaultFalse()
            ->end()
            ->end()
            ->end()
            ->end()
            ->end()
        ;
    }
}
