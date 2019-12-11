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

namespace Zumo\ZumokitBundle\Model;

/**
 * Class AppCredentials represents a model of a user account with
 * all properties required to authenticate to the RIP service.
 *
 * @package      Zumo\ZumokitBundle\Model
 * @author       Vladimir Strackovski <vladimir.strackovski@dlabs.si>
 * @copyright    2018 DLabs (https://www.dlabs.si)
 */
class AppCredentials
{
    /**
     * @var string
     */
    private $appId;

    /**
     * @var string
     */
    private $apiKey;

    /**
     * AppCredentials constructor.
     *
     * @param string $appId
     * @param string $apiKey
     */
    public function __construct(string $appId, string $apiKey)
    {
        $this->appId  = $appId;
        $this->apiKey = $apiKey;
    }

    /**
     * @return string
     */
    public function getAppId(): string
    {
        return $this->appId;
    }

    /**
     * @return string
     */
    public function getApiKey(): string
    {
        return $this->apiKey;
    }
}
