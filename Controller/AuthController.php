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

use Exception;
use Zumo\ZumokitBundle\Model\ZumoApp;
use Zumo\ZumokitBundle\Service\Client\ZumokitApiClient;
use Zumo\ZumokitBundle\Security\Token\JWTEncoder;
use Symfony\Component\Security\Core\User\UserInterface;
use Zumo\ZumokitBundle\Service\Request\Validator\RequestValidator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Psr\Log\LoggerInterface;
use App\Entity\User;
use App\Entity\Wallet;
use App\Repository\UserRepository;


/**
 * Class AuthController
 *
 * @package      Zumo\ZumokitBundle\Controller
 * @author       Vladimir Strackovski <vladimir.strackovski@dlabs.si>
 * @copyright    2018 DLabs (https://www.dlabs.si)
 */
class AuthController extends AbstractController
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var ZumoApp
     */
    private $app;

    /**
     * @var ZumokitApiClient
     */
    private $zumokitApiClient;

    /**
     * @var JWTEncoder
     */
    private $tokenEncoder;

    /**
     * @var RequestValidator
     */
    private $validator;

    /**
     * AuthController constructor.
     *
     * @param ZumoApp                   $app
     * @param ZumokitApiClient          $zumokitApiClient
     * @param JWTEncoder                $encoder
     * @param RequestValidator          $validator
     * @param LoggerInterface           $logger
     * @param UserRepository            $repository
     */
    public function __construct(
        ZumoApp $app,
        ZumokitApiClient $zumokitApiClient,
        JWTEncoder $encoder,
        RequestValidator $validator,
        LoggerInterface $logger
    ) {
        $this->app = $app;
        $this->zumokitApiClient = $zumokitApiClient;
        $this->tokenEncoder = $encoder;
        $this->validator = $validator;
        $this->logger = $logger;
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
     * @throws Exception if user object is not valid
     * @throws Exception API key is missing
     * @return JsonResponse
     */
    public function getZumokitToken(Request $request, UserInterface $userProvidedByAuthorizationToken): JsonResponse
    {
        $appUserId = $userProvidedByAuthorizationToken->getId();

        $doctrine = $this->getDoctrine();
        $userRepository = $doctrine->getRepository(User::class);
        $user = $userRepository->findOneBy(['id' => $appUserId]);

        // Check if user exist
        if (empty($user)) {
            $this->logger->critical(sprintf('User %s not found.', $appUserId));
            throw new Exception('User not found.');
        }

        $result = $this->zumokitApiClient->getTokens($user);
        return new JsonResponse($result, 200, [], true);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function syncWallets(Request $request)
    {
        // Decoded request payload is expected to have the following structure:
        // [{"id":"ID of the user", "accounts": [{"chainId":"", "address":"", "coin":"", "symbol":"", "path":""}]}]

        $doctrine = $this->getDoctrine();
        $em = $doctrine->getManager();
        $userRepository = $doctrine->getRepository(User::class);
        $walletRepository = $doctrine->getRepository(Wallet::class);

        // Decode request payload to array.
        $payload = json_decode($request->getContent(), true);

        // Iterate each payload item.
        $successItems = [];
        $numOfAccounts = 0;
        if (is_array($payload)) foreach ($payload as $item) {
            // Check if topmost required keys exist in array.
            if (!array_key_exists('id', $item) || !array_key_exists('accounts', $item)) {
                $this->logger->critical('Missing id/accounts key(s).');
                continue;
            }

            $appUserId = $item['id'];

            // Search for user in database
            $userObj = $userRepository->findOneBy(['id' => $appUserId]);

            // Check if user exist
            if (empty($userObj)) {
                $this->logger->critical(sprintf('User not %s found', $appUserId));
                continue;
            }

            // Iterate and get first account, its address and create new or update existing wallet account
            foreach ($item['accounts'] as $account) {
                try {

                    // Check if wallet already exist
                    $wallet = $walletRepository->findOneBy(['user' => $userObj->getId(), 'path' => $account['path'], 'network' => $account['network']]);

                    // Create new wallet if it does not exist
                    if (empty($wallet)) {
                        $wallet = new Wallet();
                        $wallet->setAddress($account['address']);
                        $wallet->setUser($userObj);
                    }

                    $wallet->setNetwork($account['network']);
                    $wallet->setAddress($account['address']);
                    $wallet->setPath($account['path']);
                    $wallet->setCoin($account['coin']);
                    $wallet->setSymbol($account['symbol']);
                    $wallet->setVersion($account['version']);

                    $em->persist($wallet);
                    $em->flush();

                    $numOfAccounts++;
                } catch (\Exception $exception) {
                    $this->logger->critical(sprintf('Failed to create wallet account for user %s, data: %s . Message: %s ', $userObj->getId(), json_encode($account), $exception->getMessage()));
                }
            }

            $successItems[] = $item;
        }

        $this->logger->info(sprintf('Synchronised %s accounts of %s users.', $numOfAccounts, count($successItems)));

        return new JsonResponse($successItems, 200);
    }
}
