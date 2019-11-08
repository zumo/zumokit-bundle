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

namespace Blockstar\ZumokitBundle\Exception;

use Throwable;

/**
 * Class SapiResponseException
 *
 * @package      Blockstar\ZumokitBundle\Exception
 * @author       Vladimir Strackovski <vladimir.strackovski@dlabs.si>
 * @copyright    2018 DLabs (https://www.dlabs.si)
 */
class SapiResponseException extends \Exception
{
    /**
     * @inheritDoc
     */
    public function __construct($message = "", $code = 404, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
