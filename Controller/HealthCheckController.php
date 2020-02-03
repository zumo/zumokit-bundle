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

use Zumo\ZumokitBundle\Service\Client\ZumokitApiClient;

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
class HealthCheckController extends AbstractController
{
    /**
     * @var ZumokitApiClient
     */
    protected $zumokitApiClient;

    /**
     * @var ZumoApp
     */
    protected $app;

    /**
     * @var SapiClient
     */
    private $sapi;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * HealthController constructor.
     *
     * @param ZumoApp $app
     * @param SapiClient $sapi
     * @param LoggerInterface $logger
     */
    public function __construct(ZumokitApiClient $zumokitApiClient, ZumoApp $app, SapiClient $sapi, LoggerInterface $logger)
    {
        $this->zumokitApiClient = $zumokitApiClient;
        $this->app    = $app;
        $this->sapi   = $sapi;
        $this->logger = $logger;
    }

    /**
     * Integration healthcheck.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function integrationHealthCheck(Request $request): JsonResponse
    {
        $data = $this->zumokitApiClient->integrationHealthcheck();

        try {
            $factory    = new RequestFactory($this->app);
            $request = $factory->create(AccountCheckRequest::class);
            $response = $this->sapi->sendRequest($request);
            if ($response === null) {
                return new JsonResponse(['status' => 'Error', 'message' => 'Integration health check failed.'], 400);
            }
        } catch (\Exception $exception) {
            $this->logger->critical(sprintf("Unable to process request, error: %s", $exception->getMessage()));
            return new JsonResponse(['status' => 'Error', 'message' => 'Could not perform health check.'], 400);
        }

        return new JsonResponse(['status' => 'OK', 'message' => "Integration health check passed."], 200);
    }

    /**
     * Callback for integration healthcheck.
     */
     public function integrationHealthCheckCallback() {
        return new JsonResponse(['status' => 'OK'], 200);
     }
}
