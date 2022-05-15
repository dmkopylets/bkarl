<?php

declare(strict_types=1);

namespace App\Features\User\UseCase\ProviderJoin;

use App\Entity\Facility\FacilityUser;
use App\Features\Facility\Service\FacilityService;
use Pheanstalk\Exception;

class Handler
{
    public function __construct(public FacilityService $facilityService) {}

    public function handle(Command $command): int
    {
        $user = $command->userId;
        $facility = $this->facilityService->findById($command->facilityId);
        if ($facility !== null) {
            $facilityUser = new FacilityUser();
            $facilityUser->setUser($user);
            $facilityUser->setFacility($facility);
            $facilityUser->setRole(FacilityUser::ROLES['admin user']);
            $facilityUser->setPosition(FacilityUser::POSITION['test']);
            $facilityUser->setStatus(FacilityUser::STATUS['Awaiting approval']);

            return $facilityUser->status;
        }

        throw new Exception('invalid facility');
    }
}