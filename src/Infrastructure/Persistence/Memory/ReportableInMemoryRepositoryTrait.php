<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Infrastructure\Persistence\Memory;

use Mashbo\CoreRepository\Domain\Filtering\FilterList;
use Mashbo\CoreRepository\Domain\Reports\Report;

trait ReportableInMemoryRepositoryTrait
{
    public function createReport(Report $report, FilterList $filters): Report
    {
        return $report;
    }
}
