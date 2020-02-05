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

namespace Zumo\ZumokitBundle\Service\EventHandler;

use Zumo\ZumokitBundle\Model\UserInterface;
use Zumo\ZumokitBundle\Model\ZumoApp;
use Zumo\ZumokitBundle\Security\Token\JWTEncoder;
use Zumo\ZumokitBundle\Service\Client\ZumokitApiClient;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Event;
use Exception;

//use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class LoginSuccessHandler
 *
 * handles the InteractiveLoginEvent by performing user
 * verification and upon success retrieves a pre-auth token,
 * which it signs and passes to the token storage service.
 *
 * @package      Zumo\ZumokitBundle\Service\EventHandler
 * @author       Vladimir Strackovski <vladimir.strackovski@dlabs.si>
 * @copyright    2018 DLabs (https://www.dlabs.si)
 */
class LoginSuccessHandler
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
     * @var JWTEncoder
     */
    protected $encoder;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * LoginSuccessHandler constructor.
     *
     * @param ZumokitApiClient $client
     * @param ZumoApp $app
     * @param JWTEncoder $encoder
     * @param LoggerInterface $logger
     */
    public function __construct(ZumokitApiClient $zumokitApiClient, ZumoApp $app, JWTEncoder $encoder, LoggerInterface $logger)
    {
        $this->zumokitApiClient = $zumokitApiClient;
        $this->app = $app;
        $this->encoder = $encoder;
        $this->logger = $logger;
    }

    /**
     * Handler entry point
     *
     * @param Event A generic event type supported by the framework, that
     *              is dispatched on authentication success.
     * @return null
     */
    public function handle($event)
    {
        if ($event instanceof JWTCreatedEvent) {
            $this->handleAuthenticationSuccessEvent($event);
        }

        return null;
    }

    /**
     * handleAuthenticationSuccessEvent method is invoked by the LoginSubscriber
     * every time a user logs in to the client app's platform.
     *
     * This method intercepts the most generic AuthenticationSuccessEvent
     * and uses the data passed to its argument to authenticate assemble the
     * request parameters for calling ZumoKit API /sapi/accounts endpoints.
     *
     * The /sapi/accounts endpoints on the ZumoKit API will allow the ZumoKit
     * Bundle to check if the user specified exists on ZumoKit API, and if not
     * to create one.
     *
     * @param JWTCreatedEvent $event
     * @return bool
     */
    protected function handleAuthenticationSuccessEvent($event): bool
    {
        $user = $event->getUser();
        if (!$user instanceof UserInterface) {
            $message = "Expected event's user to be an instance of UserInterface.";
            $this->logger->critical($message);
            throw new Exception($message);
        }

        try {
            if ($this->zumokitApiClient->checkIfUserAccountExists($user) === false) {
                $this->zumokitApiClient->createUserAccount($user);
            }
        } catch(Exception $exception) {
            $this->logger->critical(sprintf("Zumokit bundle: failed to check or create user account. %s", $exception->getMessage()));
            return false;
        }

        return true;
    }
}
