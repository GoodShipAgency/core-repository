<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Infrastructure\Persistence\Doctrine;

use Mashbo\CoreRepository\Domain\Pagination\LimitOffsetPage;
use Mashbo\CoreRepository\Domain\Pagination\PagedResult;
use Mashbo\CoreRepository\Domain\SearchResults;

interface PaginatedQueryExecutorInterface
{
    /**
     * @param callable(\ArrayIterator, PagedResult|null): SearchResults $callback
     *
     * @psalm-suppress MixedArgument
     * @psalm-suppress MixedMethodCall
     */
    public function execute(?LimitOffsetPage $page, callable $callback): mixed;
}
