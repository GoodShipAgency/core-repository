<?php

namespace Mashbo\CoreRepository\Infrastructure\Persistence\Doctrine;

use Mashbo\CoreRepository\Domain\Pagination\LimitOffsetPage;
use Mashbo\CoreRepository\Domain\Pagination\PagedResult;

interface PaginatedQueryExecutorInterface
{
    /**
     * @param callable(\ArrayIterator, ?PagedResult) $callback
     * @psalm-suppress MixedArgument
     * @psalm-suppress MixedMethodCall
     */
    public function execute(?LimitOffsetPage $page, callable $callback): mixed;
}