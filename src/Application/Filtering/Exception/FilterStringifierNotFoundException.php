<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Application\Filtering\Exception;

use Mashbo\CoreRepository\Domain\Filtering\Filter;

class FilterStringifierNotFoundException extends \RuntimeException
{
    protected string $filterName;

    public function __construct(Filter $filter)
    {
        $reflectionClass = new \ReflectionClass($filter);
        $this->filterName = $reflectionClass->getShortName();

        parent::__construct("Stringifier has not been defined for {$this->filterName}.");
    }

    public function getFilterName(): string
    {
        return $this->filterName;
    }
}
