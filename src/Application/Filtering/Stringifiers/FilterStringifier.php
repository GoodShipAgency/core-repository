<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Application\Filtering\Stringifiers;

use Mashbo\CoreRepository\Domain\Filtering\Filter;

/**
 * @template FilterType of Filter
 *
 * @template-implements Stringifier<Filter>
 */
abstract class FilterStringifier implements Stringifier
{
    public const FILTER_DATE_FORMAT = 'jS M Y';

    /**
     * @var FilterType|null
     */
    private ?Filter $filter = null;

    /**
     * @param FilterType $filter
     *
     * @psalm-suppress MoreSpecificImplementedParamType
     *
     * @return $this
     */
    public function with(Filter $filter): self
    {
        if (!is_a($filter, $this->getSupportedClass())) {
            $filterClass = $filter::class;
            throw new \LogicException("{$filterClass} is not an instance of {$this->getSupportedClass()}");
        }

        $this->filter = $filter;

        return $this;
    }

    /**
     * @return FilterType
     */
    public function getFilter(): Filter
    {
        if ($this->filter === null) {
            throw new \LogicException('Set the filter first using with().');
        }

        return $this->filter;
    }
}
