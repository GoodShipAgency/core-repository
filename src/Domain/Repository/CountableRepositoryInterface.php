<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Domain\Repository;

use Mashbo\CoreRepository\Domain\Filtering\FilterList;

interface CountableRepositoryInterface
{
    public function count(FilterList $filters): int;
}
