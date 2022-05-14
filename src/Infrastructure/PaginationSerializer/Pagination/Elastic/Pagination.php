<?php

declare(strict_types=1);

namespace App\Infrastructure\PaginationSerializer\Pagination\Elastic;

use App\Infrastructure\PaginationSerializer\Pagination\PaginationInterface;
use App\Services\Elastic\Result\ResultBuilderInterface;
use Knp\Component\Pager\Pagination\PaginationInterface as Paginator;

/**
 * Class Pagination
 * @package App\Infrastructure\PaginationSerializer\Pagination\Elastic
 */
class Pagination implements PaginationInterface
{
    /**
     * @var Paginator
     */
    private $paginator;

    /**
     * @var ResultBuilderInterface
     */
    private $resultBuilder;

    /**
     * Pagination constructor.
     * @param Paginator $paginator
     * @param ResultBuilderInterface $resultBuilder
     */
    public function __construct(Paginator $paginator, ResultBuilderInterface $resultBuilder)
    {
        $this->paginator = $paginator;
        $this->resultBuilder = $resultBuilder;
    }

    /**
     * @TODO переписать по-нормальному
     * @return iterable
     */
    public function getItems(): iterable
    {
        return $this->resultBuilder->build($this->paginator->getItems(),
            $this->paginator->getCustomParameter('aggregations')[$this->resultBuilder::MODULE_NAME]['buckets'][0]['analytics'] ?? []);
    }

    /**
     * @TODO переписать по-нормальному
     * @return iterable
     */
    public function getTotalAnalytics(): iterable
    {
        $analytics = $this->paginator->getCustomParameter('aggregations')[$this->resultBuilder::MODULE_NAME]['buckets'][0] ?? [];
        unset($analytics['doc_count'], $analytics['analytics']);

        return $this->resultBuilder->buildTotalAnalytics($analytics);
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
        return (int)ceil($this->paginator->getTotalItemCount() / $this->paginator->getItemNumberPerPage());
    }
}