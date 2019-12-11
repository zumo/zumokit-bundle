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

namespace Zumo\ZumokitBundle\Service\Request\SAPI;

/**
 * Class AccessTokenRequest
 *
 * @package Zumo\ZumokitBundle\Service\Request
 * @author  Vladimir Strackovski <vladimir.strackovski@dlabs.si>
 */
class AccessTokenRequest extends SapiRequest
{
    /**
     * @inheritDoc
     */
    public function getEndpointUri(): string
    {
        return 'sapi/authentication/token';
    }

    /**
     * @return string
     */
    public function getRealm(): string
    {
        return 'https://k.it/sapi/authentication/token';
    }

    /**
     * @inheritDoc
     */
    public function getMethod()
    {
        return 'POST';
    }
}
