<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Domain\Reports;

use League\Csv\AbstractCsv;

interface CsvBuilder
{
    /**
     * @param string[] $headers
     */
    public function create(array $headers, iterable $records): AbstractCsv;

    public function setDelimiter(string $delimiter): void;
}
