<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Domain\Reports;

use League\Csv\AbstractCsv;

interface CsvBuilder
{
    /**
     * @param string[]                                                             $headers
     * @param iterable<mixed, array<array-key, \Stringable|float|int|string|null>> $records
     */
    public function create(array $headers, iterable $records): AbstractCsv;

    public function setDelimiter(string $delimiter): void;
}
