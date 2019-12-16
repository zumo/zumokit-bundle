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

namespace Zumo\ZumokitBundle\EventSubscriber;

use Zumo\ZumokitBundle\Service\EventHandler\LoginSuccessHandler;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Event\AuthenticationSuccessEvent;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

/**
 * Class LoginSubscriber subscribes to successful user login events
 * and dispatches the designated handler(s).
 *
 * @package      Zumo\ZumokitBundle\EventSubscriber
 * @author       Vladimir Strackovski <vladimir.strackovski@dlabs.si>
 * @copyright    2018 DLabs (https://www.dlabs.si)
 */
class LoginSubscriber implements EventSubscriberInterface
{
    /**
     * @var LoginSuccessHandler
     */
    protected $handler;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * LoginSubscriber constructor.
     *
     * @param LoginSuccessHandler $handler
     * @param LoggerInterface     $logger
     */
    public function __construct(LoginSuccessHandler $handler, LoggerInterface $logger)
    {
        $this->handler = $handler;
        $this->logger  = $logger;
    }

    /**
     * Returns an array of events this subscriber is subscribed to.
     *
     * The event listeners and subscribers have to subscribe to one or more
     * specific events, depending on the app's integration type.
     *
     * @return array The subscribed events.
     */
    public static function getSubscribedEvents()
    {
        return [
            AuthenticationSuccessEvent::class => [['onAuthSuccess', 10]]
        ];
    }

    /**
     * @param \Symfony\Component\Security\Core\Event\AuthenticationSuccessEvent $event
     */
    public function onAuthSuccess(AuthenticationSuccessEvent $event): void
    {
        try {
            $this->handler->handle($event);
        } catch (\Exception $exception) {
            $this->logger->critical('Authentication error: ' . $exception->getMessage());
        }
    }

    /**
     * Dispatches the JWT created event handler.
     *
     * @param JWTCreatedEvent $event The event to handle.
     *
     * @return void
     */
    public function dispatchJwtHandler(JWTCreatedEvent $event): void
    {
        try {
            $this->handler->handle($event);
        } catch (\Exception $exception) {
            $this->logger->critical('Authentication error (JWT): ' . $exception->getMessage());
        }
    }

    /**
     * Dispatches the interactive login event handler.
     *
     * @param InteractiveLoginEvent $event The event to handle.
     *
     * @return void
     * @todo The re-implementation of this method is scheduled to a later time.
     *
     */
    public function dispatchInteractiveHandler(InteractiveLoginEvent $event): void
    {
        $this->logger->critical(
            sprintf(
                'Event dispatched to a non-implemented handler, event was %s.',
                $event->getAuthenticationToken()
                    ->serialize()
            )
        );
    }
}
