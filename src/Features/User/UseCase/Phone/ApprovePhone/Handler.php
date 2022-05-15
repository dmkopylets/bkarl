<?php

declare(strict_types=1);

namespace App\Features\User\UseCase\Phone\ApprovePhone;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Features\User\Repository\PhoneConfirmRepository;

class Handler
{
    public function __construct(public PhoneConfirmRepository $phoneConfirmRepository, public UserPasswordHasherInterface $passwordHasher) {}

    public function handle(Command $command): string
    {
        if(!$phoneConfirm = $this->phoneConfirmRepository->getValidPin($command->pin, $command->phone)) {
            throw new \Exception('Not Valid Code');
        }

        return $phoneConfirm->getUser()->formatPassword();
    }
}