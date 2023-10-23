<?php

namespace Mashbo\CoreRepository\Infrastructure\Persistence\Doctrine;

use Doctrine\ORM\QueryBuilder;
use Mashbo\CoreRepository\Domain\Filtering\Filter;

/** @template T of Filter */
interface DoctrineFilterHandler
{
    /** @param T $filter */
    public function handle(QueryBuilder $qb, Filter $filter): QueryBuilder;
}