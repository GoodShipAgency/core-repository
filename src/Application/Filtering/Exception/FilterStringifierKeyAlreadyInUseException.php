<?php

namespace Mashbo\CoreRepository\Application\Filtering\Exception;

use Mashbo\CoreRepository\Application\Filtering\Stringifiers\FilterStringifier;
use Mashbo\CoreRepository\Domain\Filtering\Filter;

class FilterStringifierKeyAlreadyInUseException extends \LogicException
{
    public function __construct(FilterStringifier $existingStringifier, FilterStringifier $newStringifier, string $key)
    {
        $reflectionClass = new \ReflectionClass($existingStringifier);
        $existingStringifierName = $reflectionClass->getShortName();

        $reflectionClass = new \ReflectionClass($newStringifier);
        $newStringifierName = $reflectionClass->getShortName();

        parent::__construct("Stringifier key {$key} for newly registered filter stringifier {$newStringifierName} is already in use by {$existingStringifierName}.");
    }
}