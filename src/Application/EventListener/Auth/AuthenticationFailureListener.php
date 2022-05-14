<?php

namespace App\Application\EventListener\Auth;

use App\Application\Http\ApiResponse;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Symfony\Component\HttpFoundation\Response;

class AuthenticationFailureListener
{
    public function onAuthenticationFailureResponse(AuthenticationFailureEvent $event)
    {
        $event->setResponse(new ApiResponse(
            [],
            false,
            'Wrong input data',
            Response::HTTP_UNAUTHORIZED,
        ));
    }

}