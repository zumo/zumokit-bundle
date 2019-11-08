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

namespace Blockstar\ZumokitBundle\Service\Request\SAPI;

/**
 * Class SyncRequest
 *
 * @package Blockstar\ZumokitBundle\Service\Request
 * @author  Vladimir Strackovski <vladimir.strackovski@dlabs.si>
 */
class SyncRequest extends SapiRequest
{
    /**
     * @inheritDoc
     */
    public function getEndpointUri(): string
    {
        return 'sapi/vault/sync';
    }

    /**
     * @return string
     */
    public function getRealm(): string
    {
        return 'https://k.it/sapi/vault/sync';
    }

    /**
     * @inheritDoc
     */
    public function getMethod()
    {
        return 'POST';
    }
}
