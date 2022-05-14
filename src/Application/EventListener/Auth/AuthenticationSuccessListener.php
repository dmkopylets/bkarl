<?php

namespace App\Application\EventListener\Auth;

use App\Entity\User\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class AuthenticationSuccessListener
{
    /**
     * @param AuthenticationSuccessEvent $event
     * @throws \Exception
     */
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {
        $data = $event->getData();
        $user = $event->getUser();

        if (!$user instanceof UserInterface) {
            return;
        }

     /*   if(method_exists($user,'getStatus') && $user->getStatus() == FacilityUserFixtures::STATUS['DELETED']){
            throw new \Exception('FacilityUserFixtures is not activated', Response::HTTP_NOT_FOUND);
        }*/

        $event->setData([
            'status' => true,
            'message' => '',
            'data' => $data,
            'code' => $event->getResponse()->getStatusCode()
        ]);
    }

}