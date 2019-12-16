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

namespace Zumo\ZumokitBundle\Service\Request\Parameters;

/**
 * Class Body
 *
 * @package Zumo\ZumokitBundle\Service\Request\Parameters
 * @author  Vladimir Strackovski <vladimir.strackovski@dlabs.si>
 */
final class Body
{
    /**
     * @internal
     * @var array
     */
    public const PARAMETERS = [
        Parameters::APP_ID  => 'app_id',
        Parameters::ACC_ID  => 'acc_id',
        Parameters::API_KEY => 'api_key',
    ];

    /**
     * @param int $parameterId
     *
     * @return mixed|null
     */
    public static function getName(int $parameterId)
    {
        return self::PARAMETERS[$parameterId] ?? null;
    }
}
