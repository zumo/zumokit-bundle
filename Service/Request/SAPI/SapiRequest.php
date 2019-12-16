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

use GuzzleHttp\Psr7\MessageTrait;
use GuzzleHttp\Psr7\Request;

/**
 * Class SapiRequest
 *
 * @package Zumo\ZumokitBundle\Service\Request
 * @author  Vladimir Strackovski <vladimir.strackovski@dlabs.si>
 */
abstract class SapiRequest extends Request
{
    use MessageTrait;

    /**
     * @var array|null
     */
    private $urlParameters;

    /**
     * SapiRequest constructor.
     *
     * @param      $baseUri
     * @param      $apiKey
     * @param null $accountId
     */
    public function __construct($baseUri, $apiKey, $accountId = null)
    {
        parent::__construct($this->getMethod(), sprintf("%s/%s", $baseUri, $this->getEndpointUri()));
        $this->setHeaders(['api-key' => $apiKey, 'account-id' => $accountId]);
    }

    /**
     * @return void
     */
    //    abstract protected function init(): void;

    /**
     * @return string|void
     */
    public function getMethod()
    {
        throw new \BadMethodCallException('Method should be implemented in concrete class.');
    }

    /**
     * @return string
     */
    abstract public function getEndpointUri(): string;

    /**
     * @return string
     */
    abstract public function getRealm(): string;

    /**
     * @return array|null
     */
    public function getUrlParameters(): ?array
    {
        return $this->urlParameters;
    }

    /**
     * @param array|null $urlParameters
     *
     * @return $this
     */
    public function setUrlParameters(?array $urlParameters): self
    {
        $this->urlParameters = $urlParameters;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getQueryString(): ?string
    {
        return http_build_query($this->urlParameters);
    }
}
