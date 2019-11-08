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

namespace Blockstar\ZumokitBundle\Service\Token\Extractor;

use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\AuthorizationHeaderTokenExtractor;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class BearerExtractor
 *
 * @package      Blockstar\ZumokitBundle\Service\Token\Extractor
 * @author       Vladimir Strackovski <vladimir.strackovski@dlabs.si>
 * @copyright    2019 DLabs (https://www.dlabs.si)
 */
class BearerExtractor
{
    /**
     * The prefix to split the token from.
     */
    private const PREFIX = 'Bearer';

    /**
     * The name of the request header to extract the value from.
     */
    private const HEADER = 'Authorization';

    /**
     * Extracts the token from the Authorization request header and the value
     * prefix ('Bearer').
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return string|false
     */
    public static function extract(Request $request)
    {
        return (new AuthorizationHeaderTokenExtractor(self::PREFIX, self::HEADER))->extract($request);
    }
}
