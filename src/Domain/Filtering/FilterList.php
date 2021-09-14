<?php

namespace Mashbo\CoreRepository\Domain\Filtering;

/**
 * @template-implements \IteratorAggregate<int, Filter>
 */
class FilterList implements \IteratorAggregate, \Countable
{
    /** @var array<int, Filter> */
    private array $filters;

    /**
     * @param array<int, Filter> $filters
     */
    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    /**
     * @return \ArrayIterator<int, Filter>
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

    public function count()
    {
        return count($this->filters);
    }
}
