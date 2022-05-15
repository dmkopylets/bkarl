<?php

declare(strict_types=1);

namespace App\Features\User\UseCase\Phone\PinGenerate;

use App\Features\User\Service\PhoneConfirmService;
use App\Features\User\Repository\UserRepository;
use App\Application\Service\Phone\SmsProvider;
use App\Features\User\Service\UserService;
use App\Entity\User\PhoneConfirm;

class Handler
{
    public function __construct(
        public PhoneConfirmService $phoneConfirmService,
        public UserRepository $userRepository,
        public SmsProvider $smsProvider,
        public UserService $userService
    ) {}

    public function handle(Command $command): PhoneConfirm
    {
        if($this->phoneConfirmService->hasSendedPinChangePhone($command->phone)) {
            throw new \Exception('Please wait 1 minute before next pin resend');
        }

        $user = $this->userService->getByPhone($command->phone);

        $phoneConfirm = new PhoneConfirm;
        $phoneConfirm->setUser($user);
        $phoneConfirm->setPin($this->phoneConfirmService->getPin());

        $this->phoneConfirmService->save($phoneConfirm);

        if(!getenv('IS_DEV_ENV')){
            $this->smsProvider->send($command->phone, (string) $phoneConfirm->getPin());
        }

        return $phoneConfirm;
    }
}