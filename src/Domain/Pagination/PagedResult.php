<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Domain\Pagination;

class PagedResult
{
    private LimitOffsetPage $requestedPage;
    private int $totalResults;
    private int $resultsReturned;

    public function __construct(LimitOffsetPage $requestedPage, int $totalResults, int $resultsReturned)
    {
        $this->requestedPage = $requestedPage;
        $this->totalResults = $totalResults;
        $this->resultsReturned = $resultsReturned;
    }

    public function getFirstResultNumber(): int
    {
        return $this->requestedPage->getOffset() + 1;
    }

    public function getLastResultNumber(): int
    {
        return $this->requestedPage->getOffset() + $this->resultsReturned;
    }

    public function getTotalResults(): int
    {
        return $this->totalResults;
    }

    public function hasPreviousPage(): bool
    {
        return $this->requestedPage->getOffset() > 0;
    }

    public function hasNextPage(): bool
    {
        return $this->totalResults > $this->getLastResultNumber();
    }

    public function getNextPageNumber(): int
    {
        return $this->requestedPage->next()->getPageNumber();
    }

    public function getPreviousPageNumber(): int
    {
        return $this->requestedPage->previous()->getPageNumber();
    }
}
