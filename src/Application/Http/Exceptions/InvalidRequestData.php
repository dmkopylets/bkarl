<?php

declare(strict_types=1);


namespace App\Application\Http\Exceptions;

use Symfony\Component\Validator\ConstraintViolationListInterface;
use Throwable;

/**
 * Class InvalidRequestData
 * @package App\Application\Http\Exceptions
 */
class InvalidRequestData extends \Exception
{
    private $validates;

    /**
     * InvalidRequestData constructor.
     * @param $validates
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($validates, $message = "", $code = 0, Throwable $previous = null)
    {
        $this->validates = $validates;

        parent::__construct($message, $code, $previous);
    }

    /**
     * @return ConstraintViolationListInterface
     */
    public function getValidates(): ConstraintViolationListInterface
    {
        return $this->validates;
    }
}