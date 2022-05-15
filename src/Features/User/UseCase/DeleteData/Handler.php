<?php

declare(strict_types=1);

namespace App\Features\User\UseCase\DeleteData;

use App\Application\Http\Validation\ValidationCheckerTrait;
use App\Features\Facility\Service\FacilityInvitationService;
use App\Features\User\Service\UserFacilityInvitationService;
use App\Features\User\Service\UserService;
use App\Entity\User\User;
use PHPUnit\Util\Exception;


class Handler
{
    use ValidationCheckerTrait;
    const DELETE_MESSAGE = 'DELETE';
    const DELETED_STATUS = 'Deleted';

    public function __construct(
        public UserService                   $userService,
        public FacilityInvitationService     $facilityInvitationService,
        public UserFacilityInvitationService $userFacilityInvitationService
    ) {}

    public function handle(Command $command): User
    {
        $confirmation = $command->deleteCommand;
        $confirmation !== self::DELETE_MESSAGE && $confirmation !== strtolower(self::DELETE_MESSAGE) ? throw new Exception('wrong confirmation') :

        $user = $this->userService->getByPhone($command->phone);

        $user->setFirstName(self::DELETED_STATUS);
        $user->setLastName(self::DELETED_STATUS);
        $user->setPhone(null);
        $user->setStatus(User::STATUS['BLOCKED']);
        $this->validate($user);

        $this->userService->save($user);

        return $user;
    }
}