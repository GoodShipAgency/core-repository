<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Infrastructure\Persistance\InMemory;

use Mashbo\CoreRepository\Domain\Filtering\Filter;
use Mashbo\CoreRepository\Domain\Filtering\FilterList;
use Mashbo\CoreRepository\Domain\Pagination\LimitOffsetPage;
use Mashbo\CoreRepository\Domain\Pagination\PagedResult;
use Mashbo\CoreRepository\Domain\SearchResults;

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