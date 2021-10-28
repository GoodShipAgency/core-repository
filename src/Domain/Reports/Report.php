<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Domain\Reports;

use Mashbo\CoreRepository\Domain\Exceptions\NoFirstResultException;
use Mashbo\CoreRepository\Domain\Filtering\FilterList;
use Mashbo\CoreRepository\Domain\Reports\Record\ReportRecord;

abstract class Report implements ReportInterface, \IteratorAggregate, \Countable
{
    protected \ArrayIterator $records;

    protected FilterList $filterList;

    public function __construct()
    {
        $this->records = new \ArrayIterator([]);
        $this->filterList = new FilterList([]);
    }

    public function withEach(callable $callable): void
    {
        /**
         * @var ReportRecord $record
         */
        foreach ($this->records as $record) {
            $callable($record);
        }
    }

    public function getIterator(): \ArrayIterator
    {
        return $this->records;
    }

    public function count(): int
    {
        /** @var \Countable $records */
        $records = $this->records;

        return count($records);
    }

    /**
     * @psalm-suppress MixedReturnStatement
     */
    public function first(): ReportRecord
    {
        /**
         * @var ReportRecord $record
         */
        foreach ($this->records as $record) {
            return $record;
        }

        throw new NoFirstResultException();
    }

    public function map(callable $callable): iterable
    {
        /**
         * @var int          $key
         * @var ReportRecord $record
         */
        foreach ($this->records as $key => $record) {
            yield $key => $callable($record);
        }
    }

    public function add(ReportRecord $record): void
    {
        $this->records->append($record);
    }

    abstract public function addRecordFromArray(array $result): void;

    public function setFilterList(FilterList $filterList): void
    {
        $this->filterList = $filterList;
    }

    public function getFilterList(): FilterList
    {
        return $this->filterList;
    }
}
