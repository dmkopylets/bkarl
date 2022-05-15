<?php

declare(strict_types=1);

namespace App\Features\User\UseCase\Phone\PinGenerate;

use App\Application\Http\DTO\BaseDataObject;
use Symfony\Component\Validator\Constraints as Assert;

final class Command implements BaseDataObject
{
    #[Assert\Length(min: 8, max: 20, minMessage: "min_lenght", maxMessage: "max_lenght")]
    #[Assert\Regex(pattern: "/^[0-9]*$/", message: "number_only")]
    public string $phone;
}