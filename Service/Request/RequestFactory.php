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

namespace Blockstar\ZumokitBundle\Service\Request;

use Blockstar\ZumokitBundle\Model\ZumoApp;
use Blockstar\ZumokitBundle\Service\Request\Parameters\Parameters;
use Blockstar\ZumokitBundle\Service\Request\SAPI\SapiRequest;
use function GuzzleHttp\Psr7\stream_for;

/**
 * Class RequestFactory
 *
 * @package Blockstar\ZumokitBundle\Service\Request\SAPI
 * @author  Vladimir Strackovski <vladimir.strackovski@dlabs.si>
 */
class RequestFactory
{
    /**
     * @var \Blockstar\ZumokitBundle\Model\ZumoApp
     */
    private $app;

    /**
     * @var string
     */
    private $accountId;

    /**
     * RequestFactory constructor.
     *
     * @param \Blockstar\ZumokitBundle\Model\ZumoApp $app
     * @param null                                   $accountId
     */
    public function __construct(ZumoApp $app, $accountId = null)
    {
        $this->app = $app;
        $this->accountId = $accountId;
    }

    /**
     * @param string $className
     *
     * @return \Blockstar\ZumokitBundle\Service\Request\SAPI\SapiRequest
     * @throws \Exception
     */
    public function create(string $className): SapiRequest
    {
        if (!class_exists($className)) {
            throw new \RuntimeException('Invalid argument: class does not exist.');
        }

        /** @var SapiRequest $request */
        $request = new $className($this->app->getApiUrl(), $this->app->getApiKey(), $this->accountId);

        if (!($request instanceof SapiRequest)) {
            throw new \Exception('Invalid argument: unsupported class.');
        }

        return $this->build($request);
    }

    /**
     * Add headers, body and/or query parameters to the given request.
     *
     * @param SapiRequest $request A SapiRequest instance.
     *
     * @return \Blockstar\ZumokitBundle\Service\Request\SAPI\SapiRequest|null
     */
    private function build(SapiRequest $request): ?SapiRequest
    {
        // todo set tokens to request!
        if (null === $request->getEndpointUri() || null === $request->getRealm()) {
            return null;
        }

        foreach ($this->app->getParameters($request->getRealm()) as $placementId => $placement) {
            if (count($placement) < 1) {
                continue;
            }
            foreach ($placement as $parameterPrototype) {
                if ($placementId === Parameters::PLC_BODY) {
                    if (array_key_exists('name', $parameterPrototype)) {
                        $request->withBody(
                            stream_for(json_encode([$parameterPrototype['name'] => $parameterPrototype['value']]))
                        );
                    }
                }
            }
        }

        return $request;
    }
}
