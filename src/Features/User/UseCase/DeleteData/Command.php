<?php

declare(strict_types=1);

namespace App\Features\User\UseCase\DeleteData;

use App\Application\Http\DTO\BaseDataObject;
use Symfony\Component\Validator\Constraints as Assert;

final class Command implements BaseDataObject
{
    public string $deleteCommand;
}