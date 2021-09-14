<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Domain;

use Mashbo\CoreRepository\Domain\Exceptions\NoFirstResultException;
use Mashbo\CoreRepository\Domain\Pagination\PagedResult;
use Exception;
use Traversable;

/**
 * @template T
 */
class SearchResults implements \IteratorAggregate, \Countable
{
    /** @var \Iterator<int, T> */
    private \Iterator $results;
    private ?PagedResult $pageInfo;

    /**
     * @param \Iterator<int, T> $results
     * @param PagedResult|null $pageInfo
     */
    public function __construct(\Iterator $results, ?PagedResult $pageInfo)
    {
        if (!is_countable($results)) {
            throw new \InvalidArgumentException("\$results iterator in " . __CLASS__ . " must be Countable");
        }

        $this->results = $results;
        $this->pageInfo = $pageInfo;
    }

    public function getPageInfo(): PagedResult
    {
        if ($this->pageInfo === null) {
            throw new \LogicException("Search was not paginated");
        }
        return $this->pageInfo;
    }

    public function withEach(callable $callable): void
    {
        foreach ($this->results as $result) {
            $callable($result);
        }
    }

    /** @return \Iterator<int, T> */
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
}
