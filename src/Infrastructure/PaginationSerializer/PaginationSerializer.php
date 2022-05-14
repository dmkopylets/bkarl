<?php

declare(strict_types=1);


namespace App\Infrastructure\PaginationSerializer;

use App\Infrastructure\PaginationSerializer\Pagination\PaginationInterface;

/**
 * Class PaginationSerializer
 * @package App\Infrastructure\Pagination
 */
class PaginationSerializer implements PaginationSerializerInterface
{
    /**
     * @param PaginationInterface $pagination
     * @return array
     */
    public function toArray(PaginationInterface $pagination): array
    {
        return [
            'count' => $pagination->count(),
            'total' => $pagination->getTotalItemCount(),
            'per_page' => $pagination->getItemNumberPerPage(),
            'page' => $pagination->getCurrentPageNumber(),
            'pages' => $pagination->getCurrentPagesNumber(),
        ];
    }
}