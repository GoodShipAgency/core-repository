<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Domain\Reports;

use Mashbo\CoreRepository\Domain\Filtering\FilterList;

interface ReportGenerator
{
    public function generate(FilterList $filters): Report;
}