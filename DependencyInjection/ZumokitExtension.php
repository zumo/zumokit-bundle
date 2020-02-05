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

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Class ZumokitExtension
 *
 * @package      Zumo\ZumokitBundle\DependencyInjection
 * @author       Vladimir Strackovski <vladimir.strackovski@dlabs.si>
 * @copyright    2018 DLabs (https://www.dlabs.si)
 */
class ZumokitExtension extends Extension
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

        $container->setParameter('zumokit.app_id', $config['app_id']);
        $container->setParameter('zumokit.api_key', $config['api_key']);
        $container->setParameter('zumokit.app_name', $config['app_name']);
        $container->setParameter('zumokit.api_url', $config['api_url']);
        $container->setParameter('zumokit.domains', $config['domains']);
        $container->setParameter('zumokit.primary_domain', $config['primary_domain']);
        $container->setParameter('zumokit.logging', $config['logging']);

        $container->setParameter('zumokit.security', $config['security']);
        $container->setParameter('zumokit.security.verify_ssl', $config['security']['verify_ssl']);
        $container->setParameter('zumokit.security.user_class', $config['security']['user_class']);
        $container->setParameter('zumokit.security.login_event', $config['security']['login_event']);
        $container->setParameter('zumokit.security.repository_class', $config['security']['repository_class']);
        $container->setParameter('zumokit.security.jwt', $config['security']['jwt']);
        $container->setParameter('zumokit.security.jwt.public_key', $config['security']['jwt']['public_key']);
        $container->setParameter('zumokit.security.jwt.private_key', $config['security']['jwt']['private_key']);
        $container->setParameter('zumokit.security.jwt.passphrase', $config['security']['jwt']['passphrase']);
        $container->setParameter('zumokit.security.jwt.shared_key', $config['security']['jwt']['shared_key']);
        $container->setParameter('zumokit.security.jwt.shared_secret', $config['security']['jwt']['shared_secret']);
        $container->setParameter('zumokit.security.jwt.keyset', $config['security']['jwt']['keyset']);

        $container->setParameter('zumokit.user_registration', $config['user_registration']);
        $container->setParameter('zumokit.user_registration.enable', $config['user_registration']['enable']);
        $container->setParameter('zumokit.user_registration.event', $config['user_registration']['event']);

        $definition = $container->getDefinition('zumokit.api.client');
        $definition = $container->getDefinition('zumokit.login_subscriber');
        $definition->addTag('kernel.event_subscriber');
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
        return 'http://zumo.money/schema/dic/zumokit-bundle';
    }
}
