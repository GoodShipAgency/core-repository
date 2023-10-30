<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Infrastructure\Persistence\Doctrine;

use Doctrine\ORM\QueryBuilder;
use Mashbo\CoreRepository\Domain\Pagination\LimitOffsetPage;
use Mashbo\CoreRepository\Domain\SearchResults;

interface PaginatedQueryExecutorInterface
{
    public function execute(QueryBuilder $queryBuilder, ?LimitOffsetPage $page): SearchResults;
}
