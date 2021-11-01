<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Infrastructure\Persistence\Doctrine;

use Doctrine\ORM\QueryBuilder;
use Mashbo\CoreRepository\Domain\Pagination\LimitOffsetPage;
use Mashbo\CoreRepository\Domain\Pagination\PagedResult;

class PaginatedQueryExecutor
{
    public function __construct(private \Closure $queryBuilder, private string $idProperty)
    {
    }

    /**
     * @param callable(\ArrayIterator, ?PagedResult) $callback
     * @psalm-suppress MixedArgument
     * @psalm-suppress MixedMethodCall
     */
    public function execute(?LimitOffsetPage $page, callable $callback): mixed
    {
        /** @var QueryBuilder $innerQueryBuilder */
        $innerQueryBuilder = call_user_func($this->queryBuilder);
        $outerQueryBuilder = clone $innerQueryBuilder;

        $innerQueryBuilder
            ->setMaxResults($page?->getLimit())
            ->setFirstResult($page?->getOffset())
            ->distinct();

        $outerQueryBuilder
            ->andWhere(
                $outerQueryBuilder->expr()->in(
                    $this->idProperty,
                    ':ids'
                )
            )->setParameter(
                'ids',
                array_map(
                    static fn (array $row): int => (int) $row['id'],
                    $innerQueryBuilder
                        ->addSelect($this->idProperty.' AS id')
                        ->getQuery()
                        ->getScalarResult()
                )
            );

        $results = new \ArrayIterator($outerQueryBuilder->getQuery()->execute());

        $pageInfo = null;
        if ($page !== null) {
            $idCountResult = (array) call_user_func($this->queryBuilder)
                ->select("COUNT(DISTINCT {$this->idProperty})")
                ->resetDQLPart('orderBy')
                ->getQuery()
                ->getScalarResult();

            // The shape of the resulting array is different based on whether the query was grouped and
            // how many rows are returned
            $count = 0;
            array_walk_recursive($idCountResult, function (string $result) use (&$count) {
                $count = (int) $count + (int) $result;
            });

            /** @var int $count */
            $pageInfo = new PagedResult(
                $page,
                $count,
                count($results)
            );
        }

        return $callback($results, $pageInfo);
    }
}
