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
 * Class Parameters
 *
 * @package Zumo\ZumokitBundle\Service\Request\Parameters
 * @author  Vladimir Strackovski <vladimir.strackovski@dlabs.si>
 */
final class Parameters
{
    /**
     * @var int The App's ID
     */
    public const APP_ID = 0;

    /**
     * @var int The User's Account ID
     */
    public const ACC_ID = 1;

    /**
     * @var int The App's API Key
     */
    public const API_KEY = 2;

    /**
     * @var int
     */
    public const PAT = 3;

    /**
     * @var int
     */
    public const AUTH = 4;

    /**
     * @var int
     */
    public const OTT = 5;

    /**
     * Placement options: header, body, token.
     *
     * @var string
     */
    public const PLC_HEADER = 10;
    public const PLC_BODY   = 01;
    public const PLC_TOKEN  = 20;
}
