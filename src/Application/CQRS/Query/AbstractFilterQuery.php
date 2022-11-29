<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Application\CQRS\Query;

use Mashbo\CoreRepository\Domain\CQRS\Query\Query;
use Mashbo\CoreRepository\Domain\Filtering\FilterList;
use Mashbo\CoreRepository\Domain\Pagination\LimitOffsetPage;

/** @psalm-consistent-constructor  */
abstract class AbstractFilterQuery implements Query
{
    protected function __construct(
        private readonly FilterList $filterList,
        private readonly ?LimitOffsetPage $limitOffsetPage = null
    ) {
    }

    protected static function createPagedFilteredQuery(FilterList $filterList, ?LimitOffsetPage $limitOffsetPage): static
    {
        return new static($filterList, $limitOffsetPage);
    }

    public function getFilterList(): FilterList
    {
        return $this->filterList;
    }

    public function getPage(): ?LimitOffsetPage
    {
        return $this->limitOffsetPage;
    }
}
