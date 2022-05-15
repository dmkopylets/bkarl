<?php

declare(strict_types=1);

namespace App\Features\User\UseCase\Edit;

use App\Application\Service\FileUploader\FileManager;
use App\Application\Service\FileUploader\S3\S3Manager;
use App\Features\User\Service\PhoneConfirmService;
use App\Features\User\Service\UserService;
use App\Entity\User\User;
use App\Application\Http\Validation\ValidationCheckerTrait;
use PHPUnit\Util\Exception;


class Handler
{
    use ValidationCheckerTrait;

    public function __construct(public UserService         $userService,
                                public FileManager         $fileManager,
                                public S3Manager           $s3Manager,
                                public PhoneConfirmService $phoneConfirmService)
    {
    }

    public function handle(Command $command): User
    {
        $user = $this->userService->getByPhone($command->phone);

        $user->setFirstName($command->firstName);

        $user->setLastName($command->lastName);

        $user->setDescription($command->description);

        $user->setCar($command->car);

        if (!$this->userService->isNotUniquePhone($command->changePhone)) {
            $user->setChangedPhone($command->changePhone);


        } elseif($command->phone != $command->changePhone) {
            throw new Exception('This Phone Number is already in use');
        }

        $this->validate($user);
        $this->userService->save($user);
        return $user;
    }
}
