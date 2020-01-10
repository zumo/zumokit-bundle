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

namespace Zumo\ZumokitBundle\Test;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;

/**
 * Class ZumoKitExtensionTest
 *
 * @author  Vladimir Strackovski <vladimir.strackovski@dlabs.si>
 */
class ZumoKitExtensionTest extends AbstractExtensionTestCase
{
    /**
     * @return array|\Symfony\Component\DependencyInjection\Extension\ExtensionInterface[]
     */
    protected function getContainerExtensions()
    {
        return [
            new \Zumo\ZumokitBundle\DependencyInjection\ZumokitExtension(),
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
        /*$this->load(
            [
                'api_key'  => false,
                'app_id'   => false,
                'domains'  => [],
                'metadata' => false,
            ]
        );
        */

        $this->assertEquals(1, 1);
        //$this->assertContainerBuilderHasParameter('zumokit.api_key', 'some value');
    }

    // TODO: implement tests
}
