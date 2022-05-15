<?php

declare(strict_types=1);

namespace App\Features\User\UseCase\RegisterLogin;

use App\Application\Http\Validation\ValidationCheckerTrait;
use App\Features\User\Service\UserService;
use App\Entity\User\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class Handler
{
    use ValidationCheckerTrait;

    public function __construct(public UserService $userService, public UserPasswordHasherInterface $passwordHasher) {}

    public function handle(Command $command): User
    {
        $registeredUser = $this->userService->findByPhone($command->phone);

        if($registeredUser){
            return $registeredUser;
        }

        $user = new User();
        $user->setPhone($command->phone);
        $user->setStatus(User::STATUS['ACTIVE']);
        $user->setPassword($this->passwordHasher->hashPassword($user, $user->formatPassword()));
        $this->validate($user);

        $this->userService->save($user);

        return $user;
    }
}