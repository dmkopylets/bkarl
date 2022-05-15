<?php

declare(strict_types=1);

namespace App\Features\User\UseCase\RegisterLogin;

use App\Application\Http\DTO\BaseDataObject;
use Symfony\Component\Validator\Constraints as Assert;

final class Command implements BaseDataObject
{
    #[Assert\Length(min: 8, max: 20, minMessage: "This field is not valid", maxMessage: "This field is not valid")]
    #[Assert\Regex(pattern: "/^[0-9]*$/", message: "This field is not valid")]
    public string $phone;
}