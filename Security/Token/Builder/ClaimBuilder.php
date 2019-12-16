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

namespace Zumo\ZumokitBundle\Security\Token\Builder;

use Zumo\ZumokitBundle\Security\Claim;

/**
 * Class ClaimBuilder
 *
 * @package Zumo\ZumokitBundle\Security\Token\Builder
 * @author  Vladimir Strackovski <vladimir.strackovski@dlabs.si>
 */
class ClaimBuilder
{
    /**
     * Builds a claim used to generate JWTs. Takes the name of the group the
     * current route belongs to (ie 'sapi, accounts, etc') in routeGroup
     * parameter, and tries to match the route with a firewall rule in order
     * to determine which type of claim to build.
     *
     * Returns the built claim on success. If no matches are found and
     * 'alwaysThrow' is false, returns an empty claim. Throws ClaimException
     * in any other case.
     *
     * @param string $routeGroup The group of routes the current URL belongs to.
     * @param bool   $throw      When set to true, build will throw a ClaimException
     *                           on the first error it encounteres. When false, build
     *                           will catch erros and return an empty instance of
     *                           the default Claim type (the most generic one).
     *
     * @return \Zumo\ZumokitBundle\Security\Claim\BaseClaim
     * @throws \Exception
     */
    public function build(string $routeGroup, bool $throw = false): Claim\BaseClaim
    {

        // Throw if second argument is true and a claim is not built so far...
        if (true === $throw) {
            throw new \Exception();
        }

        // ...or just an empty claim of the most generic type by default.
        return new Claim\OTT();
    }
}
