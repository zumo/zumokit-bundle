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

use Zumo\ZumokitBundle\DependencyInjection\ZumokitExtension;
use Zumo\ZumokitBundle\DependencyInjection\Configuration;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionConfigurationTestCase;

class ConfigurationTest extends AbstractExtensionConfigurationTestCase
{
    protected function getContainerExtension()
    {
        return new ZumokitExtension();
    }

    protected function getConfiguration()
    {
        return new Configuration();
    }

    /**
     * @test
     */
    public function it_converts_extension_elements_to_extensions()
    {
        $expectedConfiguration = array(
            'domain' => [
                [
                    'ad'
                ]
            ]
        );

        $sources = array(
            __DIR__ . '/Fixtures/config.yml',
            //            __DIR__ . '/Fixtures/config.xml',
        );

        $this->assertProcessedConfigurationEquals($expectedConfiguration, $sources);
    }
}
