<?php

declare(strict_types=1);

namespace App\Features\User\UseCase\Phone\isUnique;


use App\Features\User\Repository\PhoneConfirmRepository;
use App\Features\User\Service\UserService;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Application\Http\Validation\ValidationCheckerTrait;


class Handler
{
    use ValidationCheckerTrait;

    public function __construct(public UserService                 $userService,
                                public PhoneConfirmRepository      $phoneConfirmRepository,
                                public UserPasswordHasherInterface $passwordHasher){}

    public function handle(Command $command)
    {

        if (!$this->phoneConfirmRepository->getValidPin($command->pin, '', $command->changePhone)) {
            throw new \Exception('Not Valid Code');
        }
        $user = $this->userService->getByPhone($command->phone);
        $user->setPhone($command->changePhone);
        $user->setChangedPhone('');
        $user->setPassword($this->passwordHasher->hashPassword($user, $user->formatPassword()));
        $this->validate($user);
        $this->userService->save($user);

        return $user->formatPassword();
    }
}