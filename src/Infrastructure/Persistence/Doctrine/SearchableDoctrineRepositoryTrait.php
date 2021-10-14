<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Infrastructure\Persistence\Doctrine;

use Mashbo\CoreRepository\Domain\Filtering\Filter;
use Mashbo\CoreRepository\Domain\Filtering\FilterList;
use Mashbo\CoreRepository\Domain\Pagination\LimitOffsetPage;
use Mashbo\CoreRepository\Domain\Pagination\PagedResult;
use Mashbo\CoreRepository\Domain\SearchResults;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

/**
 * @template T
 */
trait SearchableDoctrineRepositoryTrait
{
    private EntityManagerInterface $manager;
    private static string $idProperty = 'id';
    private static string $alias = 'r';

    private abstract function applySearchFilter(QueryBuilder $qb, Filter $filter): QueryBuilder;

    private function getEntityManager(): EntityManagerInterface
    {
        return $this->manager;
    }

    /**
     * @psalm-suppress MixedReturnTypeCoercion
     * @return SearchResults<T>
     */
    public function search(FilterList $filters, ?LimitOffsetPage $page): SearchResults
    {
        $paginator = new PaginatedQueryExecutor(
            function () use ($filters) {
                return $this->getFilteredQueryBuilder($filters);
            },
            static::getAliasedIdProperty()
        );

        /** @var SearchResults<T> $results */
        $results = $paginator->execute(
            $page,
            /**
             * @param $results \ArrayIterator<T>
             * @return SearchResults<T>
             */
            function (\ArrayIterator $results, ?PagedResult $pageInfo): SearchResults {
                return new SearchResults(
                    $results,
                    $pageInfo
                );
            }
        );

        return $results;
    }

    private function getFilteredQueryBuilder(FilterList $filters): QueryBuilder
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select(static::$alias)
            ->from(static::$class, static::$alias);

        $_appliedFilters = [];

        foreach ($filters->getIterator() as $filter) {
            $qb = $this->filterQueryBuilder($qb, $filter, $_appliedFilters);
        }

        $qb->addOrderBy(static::getAliasedIdProperty(), 'ASC');

        return $qb;
    }

    private function filterQueryBuilder(QueryBuilder $qb, Filter $filter, array &$appliedFilters): QueryBuilder
    {
        if (in_array($filter, $appliedFilters, false)) {
            return $qb;
        }

        $appliedFilters[] = $filter;

        if ($filter instanceof FilterList) {
            foreach ($filter->getIterator() as $childFilter) {
                $this->filterQueryBuilder($qb, $childFilter, $appliedFilters);
            }

            return $qb;
        }

        return $this->applySearchFilter($qb, $filter);
    }

    private static function getAliasedIdProperty(): string
    {
        return sprintf('%s.%s', static::$alias, static::$idProperty);
    }
}