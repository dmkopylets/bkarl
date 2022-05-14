<?php

namespace App\Application\EventListener\Auth;


use App\Application\Http\ApiResponse;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTExpiredEvent;
use Symfony\Component\HttpFoundation\Response;

class JWTExpiredListener
{
    /**
     * @param JWTExpiredEvent $event
     */
    public function onJWTExpired(JWTExpiredEvent $event)
    {
        $event->setResponse(new ApiResponse(
                [],
          false,
      'Your token is expired, please renew it.',
        Response::HTTP_UNAUTHORIZED,
        ));
    }

}