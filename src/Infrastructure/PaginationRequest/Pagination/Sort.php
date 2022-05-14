<?php

declare(strict_types=1);

namespace App\Infrastructure\PaginationRequest\Pagination;

/**
 * Class Sort
 * @package App\Infrastructure\PaginationRequest\Pagination
 */
class Sort
{
    /**
     * @var string
     */
    private $column;

    /**
     * @var string
     */
    private $direction;

    /**
     * Sort constructor.
     * @param array $sort
     */
    public function __construct(array $sort)
    {
        $this->column = $sort['column'] ?? '';
        $this->direction = $sort['direction'] ?? '';
    }

    /**
     * @return string
     */
    public function getColumn(): string
    {
        return $this->column;
    }

    /**
     * @return string
     */
    public function getDirection(): string
    {
        return $this->direction;
    }
}