<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Domain;

use Mashbo\CoreRepository\Domain\Exception\NoFirstResultException;
use Mashbo\CoreRepository\Domain\Pagination\PagedResult;

/**
 * @template T
 *
 * @template-implements \Traversable<int|string, T>
 * @template-implements \IteratorAggregate<int|string, T>
 */
class SearchResults implements \Traversable, \IteratorAggregate, \Countable
{
    /** @var \Iterator<int|string, T> */
    private \Iterator $results;
    private ?PagedResult $pageInfo;

    /**
     * @param \Iterator<int|string, T> $results
     */
    public function __construct(\Iterator $results, ?PagedResult $pageInfo)
    {
        if (!is_countable($results)) {
            throw new \InvalidArgumentException('$results iterator in ' . __CLASS__ . ' must be Countable');
        }

        $this->results = $results;
        $this->pageInfo = $pageInfo;
    }

    public function getPageInfo(): PagedResult
    {
        if ($this->pageInfo === null) {
            throw new \LogicException('Search was not paginated');
        }

        return $this->pageInfo;
    }

    public function withEach(callable $callable): void
    {
        foreach ($this->results as $result) {
            $callable($result);
        }
    }

    /** @return \Iterator<int|string, T> */
    public function getIterator(): \Iterator
    {
        return $this->results;
    }

    public function count(): int
    {
        /** @var \Countable $results */
        $results = $this->results;

        return count($results);
    }

    /**
     * @return T
     */
    public function first(): object
    {
        foreach ($this->results as $result) {
            return $result;
        }

        throw new NoFirstResultException();
    }

    public function map(callable $callable): iterable
    {
        foreach ($this->results as $key => $result) {
            yield $key => $callable($result);
        }
    }

    public function toArray(): array
    {
        return iterator_to_array($this->results);
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    /**
     * @param \Closure(T):string|int $callable
     * @return SearchResults<T>
     */
    public function keyBy(callable $callable): SearchResults
    {
        $results = [];
        foreach ($this->results as $result) {
            $results[$callable($result)] = $result;
        }

        return new SearchResults(new \ArrayIterator($results), $this->pageInfo);
    }
}
