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

namespace Blockstar\ZumokitBundle\Security\Token\Builder;

use Blockstar\ZumokitBundle\Service\Request\Parameters\Header;

/**
 * Class Firewall stores authorization realms:
 *
 *   - /sapi -> The ZumoKit SAPI
 *   - /api  -> The ZumoPay API
 *   - /app  -> Mobile application
 *   - /sdk  -> ZumoKit Native C++ SDK
 *   -/
 *
 * @package Blockstar\ZumokitBundle\Security\Token\Builder
 * @author  Vladimir Strackovski <vladimir.strackovski@dlabs.si>
 */
final class Firewall
{
    /**
     * @param string|null $route
     *
     * @return array
     */
    public function getRules(?string $route = null): ?array
    {
        if (null === $route) {
            return $this->getMap();
        }

        if (array_key_exists($route, $this->getMap())) {
            return $this->getMap()[$route];
        }

        return null;
    }

    /**
     * @return array
     */
    private function getMap(): array
    {
        return [
            // app level call, no user interaction
            '/sapi/accounts/check'         => [
                'headers' => [Header::PARAMETERS[5]],
                'body'    => [],
            ],
            '/sapi/accounts/push'          => [
                'headers' => [Header::PARAMETERS[5]],
            ],
            // Vault
            '/sapi/vault/read'             => [
                'headers' => [Header::PARAMETERS[4], Header::PARAMETERS[2]],
                'body'    => [],
            ],
            '/sapi/vault/write'            => [
                'headers' => [Header::PARAMETERS[4] . Header::PARAMETERS[2]],
                'body'    => [],
            ],
            // Authorization realm
            '/sapi/authentication/request' => [
                'headers' => [Header::PARAMETERS[2], Header::PARAMETERS[1], Header::PARAMETERS[0]],
                'body'    => [],
            ],
        ];
    }
}
