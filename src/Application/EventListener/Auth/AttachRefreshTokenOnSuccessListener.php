<?php

namespace App\Application\EventListener\Auth;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;

use Gesdinet\JWTRefreshTokenBundle\EventListener\AttachRefreshTokenOnSuccessListener as AttachRefreshTokenOnSuccessListenerBasr;

class AttachRefreshTokenOnSuccessListener extends AttachRefreshTokenOnSuccessListenerBasr
{

    public function attachRefreshToken(AuthenticationSuccessEvent $event)
    {
        parent::attachRefreshToken($event);

        $data = $event->getData();

        $valueRefreshToken = $data[$this->tokenParameterName];
        unset($data[$this->tokenParameterName]);
        $data['data'][$this->tokenParameterName] = $valueRefreshToken;

        $event->setData($data);
    }
}
