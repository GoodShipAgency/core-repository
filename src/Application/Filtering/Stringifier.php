<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Application\Filtering;

use Mashbo\CoreRepository\Domain\Filtering\Filter;

/**
 * @template T0 of Filter
 */
interface Stringifier
{
    /**
     * @return class-string<Filter>
     */
    public function getSupportedClass(): string;

    /**
     * @param T0 $filter
     */
    public function with(Filter $filter): self;

    public function getLabel(): string;

    public function getValue(): string;

    public function getKey(): string;
}
