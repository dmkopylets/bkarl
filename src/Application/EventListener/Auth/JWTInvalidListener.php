<?php

namespace App\Application\EventListener\Auth;

use App\Application\Http\ApiResponse;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTInvalidEvent;
use Symfony\Component\HttpFoundation\Response;

class JWTInvalidListener
{
    /**
     * @param JWTInvalidEvent $event
     */
    public function onJWTInvalid(JWTInvalidEvent $event)
    {
        $event->setResponse(new ApiResponse(
            [],
            false,
            'Your token is invalid, please login again to get a new one',
            Response::HTTP_UNAUTHORIZED,
            ['WWW-Authenticate' => 401]
        ));
    }

}