<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Infrastructure\Persistence\Doctrine;

use Doctrine\ORM\QueryBuilder;
use Mashbo\CoreRepository\Domain\Filtering\FilterList;
use Mashbo\CoreRepository\Domain\Reports\Report;

trait ReportableDoctrineRepositoryTrait
{
    abstract protected function reportQueryBuilder(QueryBuilder $qb, Report $report): QueryBuilder;

    abstract protected function getFilteredQueryBuilder(FilterList $filters): QueryBuilder;

    /**
     * @psalm-suppress MoreSpecificReturnType
     */
    public function createReport(Report $report, FilterList $filters): Report
    {
        $qb = $this->getFilteredQueryBuilder($report->getFilterList()->merge($filters));
        $qb = $this->reportQueryBuilder($qb, $report);

        $results = $qb->getQuery()->toIterable();

        /** @var array<int, array<string, mixed>> $results */
        foreach ($results as $result) {
            $report->addRecordFromArray($result);
        }

        /*
         * @psalm-suppress InvalidStringClass
         * @psalm-suppress LessSpecificReturnStatement
         */
        return $report;
    }
}