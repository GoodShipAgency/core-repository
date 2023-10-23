<?php

namespace Mashbo\CoreRepository\Infrastructure\Persistence\Memory;

use Mashbo\CoreRepository\Domain\Filtering\Filter;

/** @template T of Filter */
interface InMemoryFilterHandler
{
    /** @param T $filter */
    public function handle(Filter $filter): bool;
}