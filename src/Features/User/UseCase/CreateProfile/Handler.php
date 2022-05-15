<?php

declare(strict_types=1);

namespace App\Features\User\UseCase\CreateProfile;

use App\Application\Http\Validation\ValidationCheckerTrait;
use App\Features\User\Service\UserService;
use App\Entity\User\User;

class Handler
{
    use ValidationCheckerTrait;

    public function __construct(public UserService $userService) {}

    public function handle(Command $command): User
    {
        $user = $this->userService->getByPhone($command->phone);

        $user->setFirstName($command->firstName);
        $user->setLastName($command->lastName);
        $user->setDescription($command->description);
        $user->setCar($command->car);
        $user->setRoles(USER::ROLES['PASSANGER']);
        $this->validate($user);

        $this->userService->save($user);

        return $user;
    }
}
