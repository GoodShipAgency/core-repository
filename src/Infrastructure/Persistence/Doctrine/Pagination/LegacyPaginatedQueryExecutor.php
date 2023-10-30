<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Infrastructure\Persistence\Doctrine\Pagination;

use Doctrine\ORM\QueryBuilder;
use Mashbo\CoreRepository\Domain\Pagination\LimitOffsetPage;
use Mashbo\CoreRepository\Domain\Pagination\PagedResult;
use Mashbo\CoreRepository\Domain\SearchResults;

class LegacyPaginatedQueryExecutor implements PaginatedQueryExecutorInterface
{
    private string $idProperty = 'id';

    public function __construct()
    {
    }

    public function execute(QueryBuilder $queryBuilder, ?LimitOffsetPage $page): SearchResults
    {
        $innerQueryBuilder = clone $queryBuilder;
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
                    /** @param array{id: int|string} $row */
                    static fn (array $row): int|string => $row['id'],
                    $innerQueryBuilder
                        ->addSelect($this->idProperty.' AS id')
                        ->getQuery()
                        ->getScalarResult()
                )
            );

        /** @psalm-suppress MixedArgument */
        $results = new \ArrayIterator($outerQueryBuilder->getQuery()->execute());

        $pageInfo = null;
        if ($page !== null) {
            /** @psalm-suppress RedundantCastGivenDocblockType */
            $idCountResult = (array) $innerQueryBuilder
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

            /** @psalm-suppress MixedArgument */
            $pageInfo = new PagedResult(
                $page,
                $count,
                count($results)
            );
        }

        return new SearchResults($results, $pageInfo);
    }
}
