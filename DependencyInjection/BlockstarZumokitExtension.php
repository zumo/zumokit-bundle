<?php

/**
 * This file is part of the blockstar/zumokit-bundle package.
 *
 * (c) DLabs / Blockstar 2019
 * Author Vladimir Strackovski <vladimir.strackovski@dlabs.si>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blockstar\ZumokitBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Class BlockstarZumokitExtension
 *
 * @package      Blockstar\ZumokitBundle\DependencyInjection
 * @author       Vladimir Strackovski <vladimir.strackovski@dlabs.si>
 * @copyright    2018 DLabs (https://www.dlabs.si)
 */
class BlockstarZumokitExtension extends Extension
{
    /**
     * @param array            $configs
     * @param ContainerBuilder $container
     *
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(dirname(__DIR__) . '/Resources/config'));
        $loader->load('services.xml');

        $configuration = $this->getConfiguration($configs, $container);

        if (!$configuration) {
            return;
        }

        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('blockstar_zumokit.app_id', $config['app_id']);
        $container->setParameter('blockstar_zumokit.api_key', $config['api_key']);
        $container->setParameter('blockstar_zumokit.app_name', $config['app_name']);
        $container->setParameter('blockstar_zumokit.api_url', $config['api_url']);
        $container->setParameter('blockstar_zumokit.domains', $config['domains']);
        $container->setParameter('blockstar_zumokit.primary_domain', $config['primary_domain']);
        $container->setParameter('blockstar_zumokit.metadata', $config['metadata']);
        $container->setParameter('blockstar_zumokit.metadata.enable', $config['metadata']['enable']);
        $container->setParameter('blockstar_zumokit.metadata.id', $config['metadata']['id']);
        $container->setParameter('blockstar_zumokit.metadata.secret', $config['metadata']['secret']);
        $container->setParameter('blockstar_zumokit.metadata.endpoint_url', $config['metadata']['endpoint_url']);
        $container->setParameter('blockstar_zumokit.metadata.root', $config['metadata']['root']);
        $container->setParameter('blockstar_zumokit.security', $config['security']);
        $container->setParameter('blockstar_zumokit.security.verify_ssl', $config['security']['verify_ssl']);
        $container->setParameter('blockstar_zumokit.security.user_class', $config['security']['user_class']);
        $container->setParameter('blockstar_zumokit.security.login_event', $config['security']['login_event']);
        $container->setParameter('blockstar_zumokit.security.repository_class', $config['security']['repository_class']);
        $container->setParameter('blockstar_zumokit.security.jwt', $config['security']['jwt']);
        $container->setParameter('blockstar_zumokit.security.jwt.public_key', $config['security']['jwt']['public_key']);
        $container->setParameter('blockstar_zumokit.security.jwt.private_key', $config['security']['jwt']['private_key']);
        $container->setParameter('blockstar_zumokit.security.jwt.passphrase', $config['security']['jwt']['passphrase']);
        $container->setParameter('blockstar_zumokit.security.jwt.shared_key', $config['security']['jwt']['shared_key']);
        $container->setParameter('blockstar_zumokit.security.jwt.shared_secret', $config['security']['jwt']['shared_secret']);
        $container->setParameter('blockstar_zumokit.security.jwt.keyset', $config['security']['jwt']['keyset']);
        $container->setParameter('blockstar_zumokit.security.jwt.well_known_url', $config['security']['jwt']['well_known_url']);
        $container->setParameter('blockstar_zumokit.user_registration', $config['user_registration']);
        $container->setParameter('blockstar_zumokit.user_registration.enable', $config['user_registration']['enable']);
        $container->setParameter('blockstar_zumokit.user_registration.event', $config['user_registration']['event']);
        $container->setParameter('blockstar_zumokit.logging', $config['logging']);
        $definition = $container->getDefinition('blockstar_zumokit.login_subscriber');
        $definition->addTag('kernel.event_subscriber');
        $definition = $container->getDefinition('blockstar_zumokit.sapi_client');
    }

    /**
     * Returns the base path for the XSD files.
     *
     * @return string The XSD base path
     */
    public function getXsdValidationBasePath()
    {
        return __DIR__ . '/../Resources/config/schema';
    }

    /**
     * @return string
     */
    public function getNamespace()
    {
        return 'http://zumopay.com/schema/dic/zumokit';
    }
}
