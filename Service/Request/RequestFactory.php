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

namespace Zumo\ZumokitBundle\Service\Request;

use Zumo\ZumokitBundle\Model\ZumoApp;
use Zumo\ZumokitBundle\Service\Request\Parameters\Parameters;
use Zumo\ZumokitBundle\Service\Request\SAPI\SapiRequest;
use function GuzzleHttp\Psr7\stream_for;

/**
 * Class RequestFactory
 *
 * @package Zumo\ZumokitBundle\Service\Request\SAPI
 * @author  Vladimir Strackovski <vladimir.strackovski@dlabs.si>
 */
class RequestFactory
{
    /**
     * @var \Zumo\ZumokitBundle\Model\ZumoApp
     */
    private $app;

    /**
     * @var string
     */
    private $accountId;

    /**
     * RequestFactory constructor.
     *
     * @param \Zumo\ZumokitBundle\Model\ZumoApp $app
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
     * @return \Zumo\ZumokitBundle\Service\Request\SAPI\SapiRequest
     * @throws \Exception
     */
    public function create(string $className): SapiRequest
    {
        if (!class_exists($className)) {
            throw new \RuntimeException('Invalid argument: class does not exist.');
        }

        $api_url = $this->app->getApiUrl();
        $api_key = $this->app->getApiKey();
        $account_id = $this->accountId;

        // Require https:// prefix in the URL
        /*
        preg_match('/^https:\/\//', $api_url, $matches);
        if (empty($matches)) {
            $api_url = "https://" . $api_url;
        }
        */

        /** @var SapiRequest $request */
        $request = new $className($api_url, $api_key, $account_id);

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
     * @return \Zumo\ZumokitBundle\Service\Request\SAPI\SapiRequest|null
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
