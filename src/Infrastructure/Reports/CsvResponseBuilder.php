<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Infrastructure\Reports;

use App\ChurchillDebtRecovery\Domain\Reports\CsvBuilder;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CsvResponseBuilder
{
    public function __construct(private CsvBuilder $csvBuilder)
    {
    }

    /**
     * @param string[] $headers
     */
    public function createStreamedResponse(array $headers, iterable $records, string $filename, ?string $delimiter = null): StreamedResponse
    {
        if ($delimiter !== null) {
            $this->csvBuilder->setDelimiter($delimiter);
        }

        $csv = $this->csvBuilder->create($headers, $records);

        $flush_threshold = 1000;

        return new StreamedResponse(
            function () use ($csv, $flush_threshold) {
                /** @var \Generator<int,string> $chunks */
                $chunks = $csv->chunk(1024);
                foreach ($chunks as $offset => $chunk) {
                    echo $chunk;
                    if ($offset % $flush_threshold === 0) {
                        flush();
                    }
                }
            },
            Response::HTTP_OK,
            [
                'Content-Encoding' => 'none',
                'Content-Description' => 'File Transfer',
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => HeaderUtils::makeDisposition(
                    ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                    $filename
                ),
            ]
        );
    }
}
