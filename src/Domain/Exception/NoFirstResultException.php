<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Domain\Exception;

class NoFirstResultException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('Could not find 1st result from set. Maybe it does not have any records');
    }
}
