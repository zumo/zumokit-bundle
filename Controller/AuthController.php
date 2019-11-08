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

use Blockstar\ZumokitBundle\Exception\AuthenticationRequestException;
use Blockstar\ZumokitBundle\Model\Wallet;
use Blockstar\ZumokitBundle\Model\ZumoApp;
use Blockstar\ZumokitBundle\Security\Token\JWTEncoder;
use Blockstar\ZumokitBundle\Service\Client\SapiClient;
use Blockstar\ZumokitBundle\Service\Request\RequestFactory;
use Blockstar\ZumokitBundle\Service\Request\SAPI\AccessTokenRequest;
use Blockstar\ZumokitBundle\Service\Request\Validator\RequestValidator;
use Blockstar\ZumokitBundle\Service\Wallet\Map;
use Blockstar\ZumokitBundle\Service\Wallet\Sync;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class AuthController
 *
 * @package      Blockstar\ZumokitBundle\Controller
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
     * @var \Blockstar\ZumokitBundle\Model\ZumoApp
     */
    private $app;

    /**
     * @var \Blockstar\ZumokitBundle\Service\Client\SapiClient
     */
    private $sapi;

    /**
     * @var \Blockstar\ZumokitBundle\Security\Token\JWTEncoder
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
     * @param \Blockstar\ZumokitBundle\Model\ZumoApp             $app
     * @param \Blockstar\ZumokitBundle\Service\Client\SapiClient $sapi
     * @param \Blockstar\ZumokitBundle\Security\Token\JWTEncoder $encoder
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
    public function syncAction(Request $request)
    {
        // Decoded request payload is expected to have the following structure:
        // [{"id":"user-zumo-id/iid", "accounts": [{"chainId":"", "address":"", "coin":"", "symbol":"", "path":""}]}]

        // Decode request payload to array.
        $inputPayload = json_decode($request->getContent(), true);
        $successItems = [];

        // Iterate each payload item.
        foreach ($inputPayload as $inputItem) {
            // Check if topmost required keys exist in array.
            if (!array_key_exists('accounts', $inputItem) || !array_key_exists('id', $inputItem)) {
                $this->logger->critical('accounts/id key(s) not present in accounts.');
                continue;
            }

            // Check id in item is not null
            if (is_null($inputItem['id'])) {
                $this->logger->critical('id is null.');
                continue;
            }

            // Search for user in database
            $userId = $inputItem['id'];
            $userObj = $this->repository->findOneBy(['id' => $userId]);

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

            // Do not assume array indexes are numeric
            $index = 0;
            // Iterate and get first account, its address and create a new local Wallet
            foreach ($inputItem['accounts'] as $inputItemAddress) {
                try {
                    $newWallet = new \App\Entity\Wallet($inputItemAddress, $userObj);
                    $userObj->setWallet($newWallet);
                    $this->repository->save($newWallet);
                } catch (\Exception $exception) {
                    $this->logger->critical(sprintf('Error saving local wallet: %s ', $exception->getMessage()));
                }

                $index++;
            }

            $successItems[] = $inputItem;
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
