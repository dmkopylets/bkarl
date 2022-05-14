<?php

declare(strict_types=1);

namespace App\Infrastructure\PaginationRequest\Pagination;

/**
 * Class Filter
 * @package App\Infrastructure\PaginationRequest\Pagination
 */
class Filter
{
    /**
     * @var array
     */
    private $filters;

    /**
     * Filter constructor.
     * @param array $filters
     */
    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    /**
     * @return array
     */
    public function getFilters(): array
    {
        return $this->filters;
    }
}