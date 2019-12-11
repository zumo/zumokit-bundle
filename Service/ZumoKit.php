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

namespace Zumo\ZumokitBundle\Service;

/**
 * Class ZumoKit
 *
 * @package      Zumo\ZumokitBundle\Service
 * @author       Vladimir Strackovski <vladimir.strackovski@dlabs.si>
 * @copyright    2019 DLabs (https://www.dlabs.si)
 */
final class ZumoKit
{
    /**
     * Constant parameters
     *
     * @var Parameter\Bag
     */
    private $parameters;

    /**
     * Static credentials
     *
     * @var Parameter\Bag
     */
    private $credentials;

    /**
     * The current app.
     *
     * @var \Zumo\ZumokitBundle\Model\ZumoApp
     */
    private $app;

    /**
     * The current user
     *
     * @var \Zumo\ZumokitBundle\Model\ZumoUser
     */
    private $user;
}
