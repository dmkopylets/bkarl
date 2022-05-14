<?php

namespace App\Application\Http\Validation;

use App\Application\Http\Exceptions\InvalidRequestData;
use Symfony\Contracts\Service\Attribute\Required;
use Symfony\Component\Validator\Validator\ValidatorInterface;

trait ValidationCheckerTrait
{
    public ValidatorInterface $validator;

    #[Required]
    public function setValidator(ValidatorInterface $validator): void
    {
        $this->validator = $validator;
    }

    public function validate($dto)
    {
        $errors = $this->validator->validate($dto);

        if (0 !== count($errors)) {

            $message = [];
            foreach ($errors as $error) {
                $message[] = $error->getMessage();
            }

            throw new InvalidRequestData($errors, implode(';', $message));
        }
    }
}