<?php

namespace Mashbo\CoreRepository\Application\Filtering\Exception;

class FilterStringifierNotFoundByKeyException extends \RuntimeException
{
    public function __construct(string $key)
    {
        parent::__construct("Stringifier has not been defined for key {$key}.");
    }
}