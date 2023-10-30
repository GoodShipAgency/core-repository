<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Infrastructure\Persistence\Doctrine;

use Doctrine\ORM\QueryBuilder;
use Mashbo\CoreRepository\Domain\Filtering\Filter;
use Mashbo\CoreRepository\Infrastructure\Persistence\Doctrine\Filter\AliasNameGeneratorInterface;
use Mashbo\CoreRepository\Infrastructure\Persistence\Doctrine\Filter\ParameterNameGeneratorInterface;

/** @implements DoctrineFilterHandler<Filter> */
class CallbackFilterHandler implements DoctrineFilterHandler
{
    /** @param (\Closure(QueryBuilder, Filter=): QueryBuilder) $callback */
    public function __construct(
        private readonly \Closure $callback
    ) {
    }

    public function handle(AliasNameGeneratorInterface $aliasNameGenerator, ParameterNameGeneratorInterface $parameterNameGenerator, QueryBuilder $qb, Filter $filter): QueryBuilder
    {
        return ($this->callback)($qb, $filter);
    }
}
