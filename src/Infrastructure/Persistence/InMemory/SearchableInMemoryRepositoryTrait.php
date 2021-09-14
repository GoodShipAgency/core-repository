<?php

declare(strict_types=1);

namespace App\ChurchillDebtRecovery\Infrastructure\Persistence\Memory;

use CoreRepository\Domain\Filtering\Filter;
use CoreRepository\Domain\Filtering\FilterList;
use CoreRepository\Domain\Pagination\LimitOffsetPage;
use CoreRepository\Domain\Pagination\PagedResult;
use CoreRepository\Domain\SearchResults;

/**
 * @template T
 */
trait SearchableInMemoryRepositoryTrait
{
    /** @param T $entity */
    abstract private function matchesFilter(mixed $entity, Filter $filter): bool;

    /** @return T[] */
    abstract public function all(): array;

    /**
     * @return SearchResults<T>
     */
    public function search(FilterList $filters, ?LimitOffsetPage $page): SearchResults
    {
        $matches = [];
        foreach ($this->all() as $key => $entity) {
            if ($this->matchesFilters($entity, $filters)) {
                $matches[$key] = $entity;
            }
        }

        if ($page !== null) {
            $matches = array_slice($matches, $page->getOffset(), $page->getLimit());

            return new SearchResults(
                new \ArrayIterator($matches),
                new PagedResult($page, count($this->all()), count($matches))
            );
        }

        return new SearchResults(new \ArrayIterator($matches), null);
    }

    /**
     * @param T $entity
     */
    private function matchesFilters(mixed $entity, FilterList $filters): bool
    {
        foreach ($filters as $filter) {
            if (!$this->matchesFilter($entity, $filter)) {
                return false;
            }
        }

        return true;
    }
}