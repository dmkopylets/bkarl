<?php

declare(strict_types=1);


namespace App\Infrastructure\PaginationSerializer;

use App\Infrastructure\PaginationSerializer\Pagination\PaginationInterface;

/**
 * Interface PaginationSerializerInterface
 * @package App\Infrastructure\Pagination
 */
interface PaginationSerializerInterface
{
    /**
     * @param PaginationInterface $pagination
     * @return array
     */
    public function toArray(PaginationInterface $pagination): array;
}