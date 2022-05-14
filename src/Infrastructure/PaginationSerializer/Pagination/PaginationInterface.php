<?php

declare(strict_types=1);


namespace App\Infrastructure\PaginationSerializer\Pagination;

/**
 * Interface PaginationInterface
 * @package App\Infrastructure\PaginationSerializer\Pagination
 */
interface PaginationInterface
{
    /**
     * @return iterable
     */
    public function getItems(): iterable;

    /**
     * @return int
     */
    public function count(): int;

    /**
     * @return int
     */
    public function getTotalItemCount(): int;

    /**
     * @return int
     */
    public function getItemNumberPerPage(): int;

    /**
     * @return int
     */
    public function getCurrentPageNumber(): int;

    /**
     * @return int
     */
    public function getCurrentPagesNumber(): int;
}