<?php

namespace App\Features\User\EmailSender;

use App\Entity\User\User;
use \Swift_Mailer;

final class ApproveRegistration
{
    public function __construct(public Swift_Mailer $mailer) { }

    public function send(User $user, string $url)
    {
        $message = (new \Swift_Message('Confirmation registration!'))
            ->setTo($user->getEmail())
            ->setBody('Registration link: ' . $url . '/approve/' . base64_encode($user->getEmail()) );

        if (!$this->mailer->send($message)) {
            throw new \RuntimeException('Unable to send register user message.');
        }
    }
}