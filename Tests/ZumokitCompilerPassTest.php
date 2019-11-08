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

namespace Blockstar\ZumokitBundle\Test;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class ZumokitCompilerPassTest
 *
 * @author  Vladimir Strackovski <vladimir.strackovski@dlabs.si>
 */
class ZumokitCompilerPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    protected function registerCompilerPass(\Symfony\Component\DependencyInjection\ContainerBuilder $container)
    {
        $container->addCompilerPass(new ZumokitCompilerPass());

        $container->setDefinition(
            'client_credentials',
            new \Symfony\Component\DependencyInjection\Definition(
                \Blockstar\ZumokitBundle\Model\ClientCredentials::class
            )
        );
    }
}
