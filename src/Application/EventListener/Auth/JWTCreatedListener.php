<?php

namespace App\Application\EventListener\Auth;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\HttpFoundation\RequestStack;

class JWTCreatedListener
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * JWTCreatedListener constructor.
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @param JWTCreatedEvent $event
     *
     * @return void
     */
    public function onJWTCreated(JWTCreatedEvent $event)
    {
        $request = $this->requestStack->getCurrentRequest();

        $payload       = $event->getData();

        //dd($payload);
/*        $userData = $this->userService->findUserByEmail($event->getUser()->getUsername());

        if($userData){
            $payload['xp'] = $userData->getXp();
            $payload['points'] = $userData->getPoints();
            $payload['avatar'] = $userData->getImage();
        }*/

        $event->setData($payload);

        $header = $event->getHeader();
        $event->setHeader($header);
    }

}