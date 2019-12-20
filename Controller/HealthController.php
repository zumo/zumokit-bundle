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

namespace Zumo\ZumokitBundle\Controller;

use Zumo\ZumokitBundle\Model\ZumoApp;
use Zumo\ZumokitBundle\Service\Client\SapiClient;
use Zumo\ZumokitBundle\Service\Request\RequestFactory;
use Zumo\ZumokitBundle\Service\Request\SAPI\AccountCheckRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Psr\Log\LoggerInterface;

/**
 * Class HealthController
 *
 * @package      Zumo\ZumokitBundle\Controller
 * @author       Vladimir Strackovski <vladimir.strackovski@dlabs.si>
 * @copyright    2019 DLabs (https://www.dlabs.si)
 */
class HealthController extends AbstractController
{
    /**
     * @var \Zumo\ZumokitBundle\Model\ZumoApp
     */
    protected $app;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var \Zumo\ZumokitBundle\Service\Client\SapiClient
     */
    private $sapi;

    /**
     * HealthController constructor.
     *
     * @param \Zumo\ZumokitBundle\Model\ZumoApp             $app
     * @param \Zumo\ZumokitBundle\Service\Client\SapiClient $sapi
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
