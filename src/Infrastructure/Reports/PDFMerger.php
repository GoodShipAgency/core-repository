<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Infrastructure\Reports;

use iio\libmergepdf\Merger;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class PDFMerger
{
    /**
     * @param iterable<string> $letters
     */
    public function createResponse(iterable $letters, string $filename): Response
    {
        $merger = new Merger();
        foreach ($letters as $letter) {
            $merger->addRaw($letter);
        }

        return new Response(
            $merger->merge(),
            Response::HTTP_OK,
            [
                'Content-Type' => 'application/pdf; charset=UTF-8',
                'Content-Disposition' => HeaderUtils::makeDisposition(
                    ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                    $filename
                ),
            ]
        );
    }
}
