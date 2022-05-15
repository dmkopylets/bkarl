<?php

declare(strict_types=1);

namespace App\Features\User\UseCase\ApproveRegistration;

use App\Application\Http\DTO\BaseDataObject;

final class Command implements BaseDataObject
{
    public string $phone;
}