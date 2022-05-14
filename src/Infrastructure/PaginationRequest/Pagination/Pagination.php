<?php

declare(strict_types=1);

namespace App\Infrastructure\PaginationRequest\Pagination;

/**
 * Class Pagination
 * @package App\Infrastructure\PaginationRequest\Pagination
 */
class Pagination
{
    /**
     * @var int
     */
    private $page;

    /**
     * @var int
     */
    private $size;

    /**
     * Pagination constructor.
     * @param int $page
     * @param int $size
     */
    public function __construct(int $page, int $size)
    {
        $this->page = $page;
        $this->size = $size;
    }

    /**
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }
}