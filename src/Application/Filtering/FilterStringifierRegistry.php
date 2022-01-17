<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Application\Filtering;

use Mashbo\CoreRepository\Application\Filtering\Exception\FilterStringifierNotFoundException;
use Mashbo\CoreRepository\Application\Filtering\Stringifiers\Stringifier;
use Mashbo\CoreRepository\Domain\Filtering\Filter;

class FilterStringifierRegistry
{
    /**
     * @var array<class-string<Filter>, Stringifier>
     */
    private array $stringifiers;

    /**
     * @param iterable<array-key, Stringifier> $stringifiers
     */
    public function __construct(iterable $stringifiers)
    {
        $this->stringifiers = [];
        foreach ($stringifiers as $stringifier) {
            $this->stringifiers[$stringifier->getSupportedClass()] = $stringifier;
        }
    }

    public function find(Filter $filter): Stringifier
    {
        $filterClass = $filter::class;
        if (array_key_exists($filterClass, $this->stringifiers)) {
            return $this->stringifiers[$filterClass]->with($filter);
        }

        throw new FilterStringifierNotFoundException($filter);
    }
}