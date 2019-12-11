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

use Zumo\ZumokitBundle\Exception\LoginHandlerException;
use Zumo\ZumokitBundle\Model\UserInterface;
use Zumo\ZumokitBundle\Model\ZumoApp;
use Zumo\ZumokitBundle\Security\Token\JWTEncoder;
use Zumo\ZumokitBundle\Service\Client\SapiClient;
use Zumo\ZumokitBundle\Service\Request\RequestFactory;
use Zumo\ZumokitBundle\Service\Request\SAPI\AccountCheckRequest;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Event;

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
     * @var \Zumo\ZumokitBundle\Service\Client\SapiClient
     */
    protected $client;

    /**
     * @var \Zumo\ZumokitBundle\Model\ZumoApp
     */
    protected $app;

    /**
     * @var \Zumo\ZumokitBundle\Security\Token\JWTEncoder
     */
    protected $encoder;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * LoginSuccessHandler constructor.
     *
     * @param SapiClient                                         $client
     * @param \Zumo\ZumokitBundle\Model\ZumoApp             $app
     * @param \Zumo\ZumokitBundle\Security\Token\JWTEncoder $encoder
     * @param \Psr\Log\LoggerInterface                           $logger
     */
    public function __construct(SapiClient $client, ZumoApp $app, JWTEncoder $encoder, LoggerInterface $logger)
    {
        $this->client = $client;
        $this->app = $app;
        $this->encoder = $encoder;
        $this->logger = $logger;
    }

    /**
     * Handler entry point
     *
     * @param Event A generic event type supported by the framework, that
     *              is dispatched on authentication success.
     *
     * @return null
     * @throws \Zumo\ZumokitBundle\Exception\LoginHandlerException
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
     * @param \Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent $event
     *
     * @return \Psr\Http\Message\ResponseInterface|null
     * @throws \Zumo\ZumokitBundle\Exception\LoginHandlerException
     */
    protected function handleAuthenticationSuccessEvent($event): ?ResponseInterface
    {
        if (!($event->getUser() instanceof UserInterface)) {
            $this->logger->critical('Expected event\'s user to be an instance of UserInterface.');
            throw new LoginHandlerException('Expected event\'s user to be an instance of UserInterface.');
        }

        try {
            $factory = new RequestFactory($this->app, (string) $event->getUser()->getId());
            $request = $factory->create(AccountCheckRequest::class);
            return $this->client->sendRequest($request);
        } catch (\Exception $exception) {
            $this->logger->critical(sprintf("Check: Response code != 200, message: %s", $exception->getMessage()));
        }

        try {
            return $this->client->pushAccount((string) $event->getUser()->getId());
        } catch (\Exception $exception) {
            if ($exception->getCode() === 404) {
                $this->logger->critical(sprintf("Push: Response code 404, message: %s", $exception->getMessage()));
            }
            $this->logger->critical(sprintf("Failed push: %s", $exception->getMessage()));
        }

        return null;
    }
}
