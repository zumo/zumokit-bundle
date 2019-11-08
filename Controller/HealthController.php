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

namespace Blockstar\ZumokitBundle\Controller;

use Blockstar\ZumokitBundle\Model\ZumoApp;
use Blockstar\ZumokitBundle\Service\Client\SapiClient;
use Blockstar\ZumokitBundle\Service\Request\RequestFactory;
use Blockstar\ZumokitBundle\Service\Request\SAPI\AccountCheckRequest;
use Blockstar\ZumokitBundle\Service\Request\SAPI\PreAuthRequest;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class HealthController
 *
 * @package      Blockstar\ZumokitBundle\Controller
 * @author       Vladimir Strackovski <vladimir.strackovski@dlabs.si>
 * @copyright    2019 DLabs (https://www.dlabs.si)
 */
class HealthController extends AbstractController
{
    /**
     * @var \Blockstar\ZumokitBundle\Model\ZumoApp
     */
    protected $app;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var \Blockstar\ZumokitBundle\Service\Client\SapiClient
     */
    private $sapi;

    /**
     * HealthController constructor.
     *
     * @param \Blockstar\ZumokitBundle\Model\ZumoApp             $app
     * @param \Blockstar\ZumokitBundle\Service\Client\SapiClient $sapi
     * @param \Psr\Log\LoggerInterface                           $logger
     */
    public function __construct(ZumoApp $app, SapiClient $sapi, LoggerInterface $logger)
    {
        $this->app    = $app;
        $this->sapi   = $sapi;
        $this->logger = $logger;
    }

    /**
     * Healthcheck action allows integration status retrieval.
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function healthCheckAction(Request $request): JsonResponse
    {
        try {
            $factory    = new RequestFactory($this->app);
            $request = $factory->create(AccountCheckRequest::class);
            if (($response = $this->sapi->sendRequest($request)) === null) {
                return new JsonResponse(['status' => 'Error', 'message' => 'Integration health check failed.'], 400);
            }
        } catch (\Exception $exception) {
            $this->logger->critical(sprintf("Unable to process request, error: %s", $exception->getMessage()));
            return new JsonResponse(['status' => 'Error', 'message' => 'Could not perform health check.'], 400);
        }

        return new JsonResponse(['status' => 'OK', 'message' => "Integration health check passed."], 200);
    }
}
