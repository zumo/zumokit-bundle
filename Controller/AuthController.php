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

use Zumo\ZumokitBundle\Exception\AuthenticationRequestException;
use Zumo\ZumokitBundle\Model\Wallet;
use Zumo\ZumokitBundle\Model\ZumoApp;
use Zumo\ZumokitBundle\Security\Token\JWTEncoder;
use Zumo\ZumokitBundle\Service\Client\SapiClient;
use Zumo\ZumokitBundle\Service\Request\RequestFactory;
use Zumo\ZumokitBundle\Service\Request\SAPI\AccessTokenRequest;
use Zumo\ZumokitBundle\Service\Request\Validator\RequestValidator;
use Zumo\ZumokitBundle\Service\Wallet\Map;
use Zumo\ZumokitBundle\Service\Wallet\Sync;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class AuthController
 *
 * @package      Zumo\ZumokitBundle\Controller
 * @author       Vladimir Strackovski <vladimir.strackovski@dlabs.si>
 * @copyright    2018 DLabs (https://www.dlabs.si)
 */
class AuthController extends AbstractController
{
    public const REPOSITORY_SUPERCLASS = "App\Repository\EntityRepository";

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var \Zumo\ZumokitBundle\Model\ZumoApp
     */
    private $app;

    /**
     * @var \Zumo\ZumokitBundle\Service\Client\SapiClient
     */
    private $sapi;

    /**
     * @var \Zumo\ZumokitBundle\Security\Token\JWTEncoder
     */
    private $tokenEncoder;

    /**
     * @var RequestValidator
     */
    private $validator;

    /**
     * @var \App\Repository\UserRepository
     */
    private $repository;

    /**
     * AuthController constructor.
     *
     * @param \Zumo\ZumokitBundle\Model\ZumoApp             $app
     * @param \Zumo\ZumokitBundle\Service\Client\SapiClient $sapi
     * @param \Zumo\ZumokitBundle\Security\Token\JWTEncoder $encoder
     * @param RequestValidator                                   $validator
     * @param \Psr\Log\LoggerInterface                           $logger
     * @param \App\Repository\UserRepository                     $repository
     */
    public function __construct(
        ZumoApp $app,
        SapiClient $sapi,
        JWTEncoder $encoder,
        RequestValidator $validator,
        LoggerInterface $logger,
        \App\Repository\UserRepository $repository
    ) {
        $this->app = $app;
        $this->sapi = $sapi;
        $this->tokenEncoder = $encoder;
        $this->validator = $validator;
        $this->logger = $logger;
        $this->repository = $repository;
    }

    /**
     * Bundle authentication endpoint - called by the client SDK to
     * retrieve an access token for calling protected endpoints
     * directly on ZumoKit API.
     *
     * Endpoint exposed at <client-api-root-url>/wallet/auth/request.
     *
     * @param Request            $request
     * @param UserInterface|null $user
     *
     * @return JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getZumoKitTokenAction(Request $request, UserInterface $user): JsonResponse
    {
        try {
            if (
                // Here we check only if the user entity has a getter for ID property.
                // Any symfony implementation will have a username and id, so this is
                // just a precaution.
                !($user instanceof UserInterface) ||
                !method_exists($user, 'getId')
            ) {
                throw new AuthenticationRequestException("Invalid user object received.");
            }

            if (!($request->headers->has('api-key'))) {
                throw new AuthenticationRequestException("Missing API KEY.");
            }
        } catch (AuthenticationRequestException $exception) {
            $this->logger->critical(sprintf("Unable to process request, error: %s", $exception->getMessage()));
            return new JsonResponse(['error' => 'Unauthorized', 'message' => 'Exception occured.'], 401);
        }

        try {
            $client = new \GuzzleHttp\Client(["base_uri" => $this->sapi->getBaseUri()]);
            $headers = [
                'account-id' => (string) $user->getId(),
                'api-key'    => $this->app->getApiKey(),
            ];

            $request = new \GuzzleHttp\Psr7\Request(
                "POST",
                sprintf('%s/sapi/authentication/token', $this->sapi->getBaseUri()),
                $headers
            );

            $response = $client->send($request);
        } catch (\Exception $exception) {
            $this->logger->critical(sprintf("Unable to process request, error: %s", $exception->getMessage()));
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        return new JsonResponse($response->getBody(), 200, [], true);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function syncWallets(Request $request)
    {
        // Decoded request payload is expected to have the following structure:
        // [{"id":"user's iid", "accounts": [{"chainId":"", "address":"", "coin":"", "symbol":"", "path":""}]}]

        // Decode request payload to array.
        $payload = json_decode($request->getContent(), true);

        // Iterate each payload item.
        $successItems = [];
        foreach ($payload as $item) {
            // Check if topmost required keys exist in array.
            if (!array_key_exists('id', $item) || !array_key_exists('accounts', $item)) {
                $this->logger->critical('Missing id/accounts key(s).');
                continue;
            }

            $appUserId = $item['id'];

            // Skip if ID is not provided or not in UUID format.
            if (is_null($appUserId) || is_int($appUserId)) {
                $this->logger->critical('Invalid ID.');
                continue;
            }

            // Search for user in database

            $userObj = $this->repository->findOneBy(['id' => $appUserId]);

            // Check if retrieved object is of correct type
            if (get_class($userObj) !== 'App\Entity\User') {
                $this->logger->critical('user obj is not of type App\Entity\User.');
                continue;
            }

            // Check if user has a wallet getter
            if (!method_exists($userObj, 'getWallet') || !method_exists($userObj, 'setWallet')) {
                $this->logger->critical('user obj does not have wallet methods.');
                continue;
            }

            // Skip item if user already has wallet associated
            if (!is_null($userObj->getWallet())) {
                $this->logger->critical('user obj already has a wallet associated.');
                continue;
            }

            // Iterate and get first account, its address and create a new local Wallet
            foreach ($item['accounts'] as $account) {
                try {
                    $wallet = new \App\Entity\Wallet($account['address'], $userObj);
                    $wallet->setCoin($account['coin']);
                    $wallet->setSymbol($account['symbol']);
                    $wallet->setNetwork($account['network']);
                    $wallet->setChainId($account['chainId']);
                    $wallet->setPath($account['path']);
                    $wallet->setVersion($account['version']);
                    $userObj->setWallet($wallet);
                    $this->repository->save($wallet);
                } catch (\Exception $exception) {
                    $this->logger->critical(sprintf('Failed to create wallet account for user %s, data: %s . Message: %s ', $userObj->getId(), json_encode($account), $exception->getMessage()));
                }
            }

            $successItems[] = $item;
        }

        return new JsonResponse($successItems, 200);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function mapWalletsAction(Request $request): JsonResponse
    {
        if (!$request->headers->has('Authorization')) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        $body = json_decode($request->getContent(), true);
        $mapped = [];

        foreach ($body as $item) {
            $mapper = new Map($this->repository, $this->logger);
            $mapped[] = $mapper->map($body);
        }

        return new JsonResponse($mapped, 200);
    }
}
