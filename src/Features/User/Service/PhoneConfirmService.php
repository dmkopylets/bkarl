<?php

namespace App\Features\User\Service;

use App\Features\User\Repository\PhoneConfirmRepository;
use App\Entity\User\PhoneConfirm;
use PHPUnit\Util\Exception;


class PhoneConfirmService
{
    private const PIN_FOR_DEV = 1111;

    public function __construct(public PhoneConfirmRepository $phoneConfirmRepository)
    {
    }

    public function get(int $pinId): ?PhoneConfirm
    {
        if (!$phoneConfirm = $this->find($pinId)) {
            throw new Exception('phoneConfirmation failed');
        }

        return $phoneConfirm;
    }

    public function find(string $pinId): ?PhoneConfirm
    {
        return $this->phoneConfirmRepository->loadPhoneConfirmByIdentifier($pinId);
    }

    public function save(PhoneConfirm $phoneConfirm): void
    {
        $this->phoneConfirmRepository->save($phoneConfirm);
    }

    public function hasSendedPin(string $phone): bool
    {
        return $this->phoneConfirmRepository->hasSendedPin($phone);
    }

    public function hasSendedPinChangePhone(string $changePhone): bool
    {
        return $this->phoneConfirmRepository->hasSendedPinChangePhone($changePhone);
    }

    public function getPin(): string
    {
        return getenv('IS_DEV_ENV') ? self::PIN_FOR_DEV : random_int(1000, 9999);
    }

}