<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Infrastructure\Reports;

use League\Csv\AbstractCsv;
use League\Csv\Reader;
use League\Csv\Writer;
use Mashbo\CoreRepository\Domain\Reports\CsvBuilder;

class LeagueCsvBuilder implements CsvBuilder
{
    private ?string $delimiter = null;

    /**
     * @param string[] $headers
     */
    public function create(array $headers, iterable $records): AbstractCsv
    {
        $csv = Writer::createFromPath('php://temp', 'r+');
        $csv->setOutputBOM(Reader::BOM_UTF8);

        if (null !== $this->delimiter) {
            $csv->setDelimiter($this->delimiter);
        }

        $csv->insertOne($headers);
        $csv->insertAll($records);

        return $csv;
    }

    public function setDelimiter(string $delimiter): void
    {
        $this->delimiter = $delimiter;
    }
}
