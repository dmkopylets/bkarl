<?php

namespace App\Application\EventListener;

use App\Application\Http\ApiResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpFoundation\Response;


class ExceptionListener
{
    /**
     * @param ExceptionEvent $event
     */
    public function onKernelException(ExceptionEvent $event)
    {
       $event->setResponse(new ApiResponse(
           (object)[],
           false,
           $event->getThrowable()->getMessage(),
           $event->getThrowable()->getCode()?: Response::HTTP_INTERNAL_SERVER_ERROR
       ));
    }

}