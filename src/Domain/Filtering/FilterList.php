<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Domain\Filtering;

/**
 * @template-implements \IteratorAggregate<int, Filter>
 */
class FilterList implements \IteratorAggregate, \Countable
{
    /** @var array<array-key, Filter> */
    private array $filters;

    /**
     * @param array<array-key, Filter> $filters
     */
    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    /**
     * @param array<array-key, Filter>|Filter $filter
     */
    public static function create(array|Filter $filter = []): self
    {
        if (!is_array($filter)) {
            $filter = [$filter];
        }

        return new self($filter);
    }

    /**
     * @return \ArrayIterator<array-key, Filter>
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->filters);
    }

    public function merge(FilterList $filtersToMerge): FilterList
    {
        return new FilterList(array_merge($this->filters, $filtersToMerge->filters));
    }

    public function append(Filter $filter): FilterList
    {
        return $this->merge(new FilterList([$filter]));
    }

    public function count(): int
    {
        return count($this->filters);
    }
}
