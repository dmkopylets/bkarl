<?php

declare(strict_types=1);

namespace App\Infrastructure\PaginationRequest;

use App\Infrastructure\PaginationRequest\Pagination\Filter;
use App\Infrastructure\PaginationRequest\Pagination\Pagination;
use App\Infrastructure\PaginationRequest\Pagination\Sort;

/**
 * Interface PaginationSerializerInterface
 * @package App\Infrastructure\Pagination
 */
interface PaginationRequestInterface
{
    /**
     * @return Filter
     */
    public function getFilter(): Filter;

    /**
     * @return Sort
     */
    public function getSort(): Sort;

    /**
     * @return Pagination
     */
    public function getPagination(): Pagination;
}