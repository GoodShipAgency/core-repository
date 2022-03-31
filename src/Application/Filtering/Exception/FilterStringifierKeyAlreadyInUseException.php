<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Application\Filtering\Exception;

use Mashbo\CoreRepository\Application\Filtering\Stringifiers\Stringifier;

class FilterStringifierKeyAlreadyInUseException extends \LogicException
{
    public function __construct(Stringifier $existingStringifier, Stringifier $newStringifier, string $key)
    {
        $reflectionClass = new \ReflectionClass($existingStringifier);
        $existingStringifierName = $reflectionClass->getShortName();

        $reflectionClass = new \ReflectionClass($newStringifier);
        $newStringifierName = $reflectionClass->getShortName();

        parent::__construct("Stringifier key {$key} for newly registered filter stringifier {$newStringifierName} is already in use by {$existingStringifierName}.");
    }
}
