<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Infrastructure\Persistence\Doctrine\Joiner;

use Doctrine\ORM\QueryBuilder;

interface JoinerInterface
{
    public function apply(QueryBuilder $queryBuilder): QueryBuilder;
}
