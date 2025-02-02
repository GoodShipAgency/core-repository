<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Infrastructure\Persistence\Memory;

use Mashbo\CoreRepository\Domain\Filtering\Filter;
use Mashbo\CoreRepository\Domain\Filtering\FilterList;
use Mashbo\CoreRepository\Domain\Filtering\OrderByFilter;
use Mashbo\CoreRepository\Domain\Pagination\LimitOffsetPage;
use Mashbo\CoreRepository\Domain\Pagination\PagedResult;
use Mashbo\CoreRepository\Domain\SearchResults;

/**
 * @template T
 */
trait SearchableInMemoryRepositoryTrait
{
    /** @param T $entity */
    abstract protected function matchesFilter(mixed $entity, Filter $filter): bool;

    /**
     * @psalm-suppress UnusedParam
     *
     * @param T $a
     * @param T $b
     */
    private function sortByFilter(mixed $a, mixed $b, Filter $filter): int
    {
        return 0;
    }

    /** @return T[] */
    abstract public function all(): array;

    public function exists(FilterList $filters): bool
    {
        foreach ($this->all() as $_key => $entity) {
            if ($this->matchesFilters($entity, $filters)) {
                return true;
            }
        }

        return false;
    }

    public function count(FilterList $filters): int
    {
        return $this->search($filters, null)->count();
    }

    /**
     * @return SearchResults<T>
     */
    public function search(FilterList $filters, LimitOffsetPage $page = null): SearchResults
    {
        $results = array_filter(
            $this->all(),
            function (mixed $entity) use ($filters): bool {
                return $this->matchesFilters($entity, $filters);
            }
        );

        /* @psalm-suppress MixedArgument */
        usort(
            $results,
            function (mixed $a, mixed $b) use ($filters) {
                return $this->sortByFilters($a, $b, $filters);
            }
        );

        if ($page !== null) {
            $pagedResults = array_slice($results, $page->getOffset(), $page->getLimit());

            return new SearchResults(new \ArrayIterator($pagedResults), new PagedResult($page, count($results), count($pagedResults)));
        }

        return new SearchResults(new \ArrayIterator($results), null);
    }

    public function batch(FilterList $filterList): \Generator
    {
        foreach ($this->all() as $_key => $entity) {
            if ($this->matchesFilters($entity, $filterList)) {
                yield $entity;
            }
        }
    }

    /**
     * @param T $entity
     */
    private function matchesFilters(mixed $entity, FilterList $filters): bool
    {
        foreach ($filters as $filter) {
            if ($filter instanceof FilterList) {
                foreach ($filter->getIterator() as $childFilter) {
                    if (!$this->matchesFilter($entity, $childFilter)) {
                        return false;
                    }
                }

                continue;
            }

            if ($filter instanceof OrderByFilter) {
                continue;
            }

            if (!$this->matchesFilter($entity, $filter)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param T $a
     * @param T $b
     */
    private function sortByFilters(mixed $a, mixed $b, FilterList $filters): int
    {
        $return = 0;
        foreach ($filters as $filter) {
            if ($filter instanceof FilterList) {
                foreach ($filter->getIterator() as $childFilter) {
                    $return = $this->sortByFilter($a, $b, $childFilter);
                }

                continue;
            }

            $return = $this->sortByFilter($a, $b, $filter);

            if ($return !== 0) {
                return $return;
            }
        }

        return $return;
    }

    private function configureFilters(): array
    {
        return [];
    }
}
