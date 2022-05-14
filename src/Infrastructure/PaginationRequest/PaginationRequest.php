<?php

declare(strict_types=1);

namespace App\Infrastructure\PaginationRequest;

use App\Infrastructure\PaginationRequest\Pagination\Filter;
use App\Infrastructure\PaginationRequest\Pagination\Pagination;
use App\Infrastructure\PaginationRequest\Pagination\Sort;

/**
 * Class PaginationRequest
 * @package App\Infrastructure\PaginationRequest
 */
class PaginationRequest implements PaginationRequestInterface
{
    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var Sort
     */
    private $sorting;

    /**
     * @var Pagination
     */
    private $pagination;

    /**
     * PaginationRequest constructor.
     * @param array $queryParams
     */
    public function __construct(array $queryParams)
    {
        $this->filter = new Filter($queryParams['filter'] ?? []);
        $this->sorting = new Sort($queryParams['sort'] ?? []);
        $this->pagination = new Pagination((int)($queryParams['page'] ?? 1), (int)($queryParams['size'] ?? 50));
    }

    /**
     * @return Filter
     */
    public function getFilter(): Filter
    {
        return $this->filter;
    }

    /**
     * @return Sort
     */
    public function getSort(): Sort
    {
        return $this->sorting;
    }

    /**
     * @return Pagination
     */
    public function getPagination(): Pagination
    {
        return $this->pagination;
    }
}