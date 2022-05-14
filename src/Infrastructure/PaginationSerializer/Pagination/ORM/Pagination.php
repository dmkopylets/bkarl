<?php

declare(strict_types=1);


namespace App\Infrastructure\PaginationSerializer\Pagination\ORM;

use App\Infrastructure\PaginationSerializer\Pagination\PaginationInterface;
use Knp\Component\Pager\Pagination\PaginationInterface as PaginationORM;

/**
 * Class Pagination
 * @package App\Infrastructure\PaginationSerializer\Pagination\ORM
 */
class Pagination implements PaginationInterface
{
    /**
     * @var PaginationORM
     */
    private $paginator;

    /**
     * Pagination constructor.
     * @param PaginationORM $paginator
     */
    public function __construct(PaginationORM $paginator)
    {
        $this->paginator = $paginator;
    }

    /**
     * @return iterable
     */
    public function getItems(): iterable
    {
        return $this->paginator->getItems();
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return $this->paginator->count();
    }

    public function getTotalItemCount(): int
    {
        return $this->paginator->getTotalItemCount();
    }

    /**
     * @return int
     */
    public function getItemNumberPerPage(): int
    {
        return $this->paginator->getItemNumberPerPage();
    }

    /**
     * @return int
     */
    public function getCurrentPageNumber(): int
    {
        return $this->paginator->getCurrentPageNumber();
    }

    /**
     * @return int
     */
    public function getCurrentPagesNumber(): int
    {
        return (int) ceil($this->paginator->getTotalItemCount() / $this->paginator->getItemNumberPerPage());
    }
}