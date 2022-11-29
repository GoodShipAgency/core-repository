<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Domain\Exception;

class NoSuchRecordException extends \RuntimeException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
