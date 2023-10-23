<?php

namespace Mashbo\CoreRepository\Infrastructure\Persistence\Doctrine\Joiner;

use Doctrine\ORM\QueryBuilder;
use Mashbo\CoreRepository\Infrastructure\Persistence\Doctrine\Filter\AliasNameGeneratorInterface;

interface JoinerInterface
{
    public function apply(QueryBuilder $queryBuilder): QueryBuilder;
}