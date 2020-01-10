<?php

namespace App\Tests\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ZumokitBundleTestIntegration extends WebTestCase
{
    private $client = null;

    public function setUp()
    {
        $this->client = static::createClient();
    }

    protected static function getKernelClass()
    {
        return \App\Kernel::class;
    }

    /**
     * The purpose of this test is to insert fixtures (see the trait) only once.
     */
    public function testAddFixtures()
    {
        $this->assertEquals(1, 1);
    }
}
