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

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;

/**
 * Class BlockstarZumoKitExtensionTest
 *
 * @author  Vladimir Strackovski <vladimir.strackovski@dlabs.si>
 */
class BlockstarZumoKitExtensionTest extends AbstractExtensionTestCase
{
    /**
     * @return array|\Symfony\Component\DependencyInjection\Extension\ExtensionInterface[]
     */
    protected function getContainerExtensions()
    {
        return [
            new \Blockstar\ZumokitBundle\DependencyInjection\BlockstarZumokitExtension(),
        ];
    }

    /**
     * @inheritDoc
     */
    protected function getMinimalConfiguration()
    {
        return [
            'api_key'  => false,
            'app_id'   => false
        ];
    }

    /**
     * @test
     */
    public function after_loading_the_correct_parameter_has_been_set()
    {

        $this->load(
            [
                'api_key'  => false,
                'app_id'   => false,
                'domains'  => [],
                'metadata' => false,
            ]
        );

        $this->assertContainerBuilderHasParameter('api_key', 'some value');
    }
}
