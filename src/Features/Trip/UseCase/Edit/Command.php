<?php

declare(strict_types=1);

namespace App\Features\Trip\UseCase\Edit;

use App\Application\Http\DTO\BaseDataObject;
use Symfony\Component\Validator\Constraints as Assert;

final class Command implements BaseDataObject
{
    #[Assert\NotBlank]
    public string $firstName = '';

    #[Assert\NotBlank]
    public string $lastName = '';

    public ?string $description = '';

    public ?string $car = '';

    public string $phone = '';

    public string $changePhone = '';



}
