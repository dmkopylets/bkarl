<?php

declare(strict_types=1);

namespace App\Features\User\UseCase\ApproveRegistration;

use App\Entity\User\User;
use App\Features\User\Repository\UserRepository;
use App\Features\User\Service\UserService;
use Webmozart\Assert\Assert;

class Handler
{
    public function __construct(public UserRepository $userRepository, public UserService $userService) {}

    public function handle(Command $command): User
    {
        $user = $this->userService->getByPhone($command->phone);

//        if(!$user->isRegistered()){
//            throw new \Exception('FacilityUserFixtures have been registered');
//        }

        $user->setStatus(User::STATUS['ACTIVE']);

        $this->userRepository->save($user);

        return $user;
    }
}