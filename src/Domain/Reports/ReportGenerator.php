<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Domain\Reports;

use Mashbo\CoreRepository\Domain\Filtering\FilterList;
use Mashbo\CoreRepository\Domain\Model\DateRange;

interface ReportGenerator
{
    public function applyDateRangeContext(DateRange $dateRange): self;

    public function generate(FilterList $filters): Report;
}
