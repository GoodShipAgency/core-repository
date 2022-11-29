<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Domain\Repository;

use Mashbo\CoreRepository\Domain\Filtering\FilterList;
use Mashbo\CoreRepository\Domain\Pagination\LimitOffsetPage;
use Mashbo\CoreRepository\Domain\SearchResults;

/** @template T */
interface SearchableRepositoryInterface
{
    /** @return SearchResults<T> */
    public function search(FilterList $filters, ?LimitOffsetPage $page): SearchResults;

    public function exists(FilterList $filters): bool;
}
