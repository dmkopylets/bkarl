<?php


namespace App\Application\EventListener\Auth;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTNotFoundEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Response\JWTAuthenticationFailureResponse;
use Symfony\Component\HttpFoundation\Response;

class JWTNotFoundListener
{
    /**
     * @param JWTNotFoundEvent $event
     */
    public function onJWTNotFound(JWTNotFoundEvent $event)
    {
        $response = new JWTAuthenticationFailureResponse('Missing token', Response::HTTP_FORBIDDEN);
        $response->setData(['status' => false, 'data' => []]);

        $event->setResponse($response);
    }

}