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
 * @template-implements \ArrayAccess<int|string, T>
 */
class SearchResults implements \Traversable, \IteratorAggregate, \Countable, \ArrayAccess
{
    /** @var \ArrayIterator<int|string, T> */
    private \ArrayIterator $results;
    private ?PagedResult $pageInfo;

    /**
     * @param \ArrayIterator<int|string, T> $results
     */
    public function __construct(\ArrayIterator $results, ?PagedResult $pageInfo)
    {
        if (!is_countable($results)) {
            throw new \InvalidArgumentException('$results iterator in '.__CLASS__.' must be Countable');
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
        return $this->results->getArrayCopy();
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    /**
     * @param \Closure(T):(array-key) $callable
     *
     * @return SearchResults<T>
     */
    public function keyBy(\Closure $callable): SearchResults
    {
        $results = [];
        foreach ($this->results as $result) {
            $results[$callable($result)] = $result;
        }

        return new SearchResults(new \ArrayIterator($results), $this->pageInfo);
    }

    /**
     * @param \Closure(T):(bool) $callable
     *
     * @return SearchResults<T>
     */
    public function filter(\Closure $closure): SearchResults
    {
        $results = [];
        foreach ($this->results as $key => $result) {
            if ($closure($result)) {
                $results[$key] = $result;
            }
        }

        return new SearchResults(new \ArrayIterator($results), $this->pageInfo);
    }

    /** @param \Closure(T):(bool) $callable */
    public function any(\Closure $callable): bool
    {
        foreach ($this->results as $result) {
            if ($callable($result)) {
                return true;
            }
        }

        return false;
    }

    /** @param \Closure(T):(bool) $callable */
    public function all(\Closure $callable): bool
    {
        foreach ($this->results as $result) {
            if (!$callable($result)) {
                return false;
            }
        }

        return true;
    }

    /** @param \Closure(T):(bool) $callable */
    public function none(\Closure $callable): bool
    {
        return !$this->any($callable);
    }

    /** @param int|string $offset */
    public function offsetExists(mixed $offset): bool
    {
        return $this->results->offsetExists($offset);
    }

    /**
     * @param int|string $offset
     *
     * @return T
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->results->offsetGet($offset);
    }

    /**
     * @param int|string $offset
     * @param T          $value
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->results->offsetSet($offset, $value);
    }

    /** @param int|string $offset */
    public function offsetUnset(mixed $offset): void
    {
        $this->results->offsetUnset($offset);
    }
}
