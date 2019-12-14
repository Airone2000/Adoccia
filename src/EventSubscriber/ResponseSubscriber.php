<?php

namespace App\EventSubscriber;

use App\Entity\User;
use Ramsey\Uuid\Uuid;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ResponseSubscriber implements EventSubscriberInterface
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function giveVisitorUniqueID(ResponseEvent $event)
    {
        /* @var User|string */

        if (!$this->tokenStorage->getToken()) {
            return;
        }

        $user = $this->tokenStorage->getToken()->getUser();
        if (\is_string($user)) { // anon.
            $request = $event->getRequest();
            $response = $event->getResponse();

            $cookieKey = '_guid'; // Guest Unique ID
            if (!$request->cookies->has($cookieKey)) {
                $expire = time() + (3600 * 24 * 7); // 7 days
                $cookie = Cookie::create($cookieKey, Uuid::uuid4()->toString(), $expire);
                $response->headers->setCookie($cookie);
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::RESPONSE => 'giveVisitorUniqueID',
        ];
    }
}
