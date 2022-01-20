<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Infrastructure\Persistence\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Mashbo\CoreRepository\Domain\Filtering\Filter;
use Mashbo\CoreRepository\Domain\Filtering\FilterList;
use Mashbo\CoreRepository\Domain\Pagination\LimitOffsetPage;
use Mashbo\CoreRepository\Domain\Pagination\PagedResult;
use Mashbo\CoreRepository\Domain\SearchResults;

/**
 * @template T
 */
trait SearchableDoctrineRepositoryTrait
{
    private EntityManagerInterface $manager;
    private array $appliedFilters = [];
    private static string $idProperty = 'id';
    private static string $alias = 'r';

    abstract protected function applyFilter(QueryBuilder $qb, Filter $filter): QueryBuilder;

    private function getEntityManager(): EntityManagerInterface
    {
        return $this->manager;
    }

    /**
     * @psalm-suppress MixedReturnTypeCoercion
     *
     * @return SearchResults<T>
     */
    public function search(FilterList $filters, ?LimitOffsetPage $page): SearchResults
    {
        $paginator = new PaginatedQueryExecutor(
            function () use ($filters) {
                $qb = $this->getFilteredQueryBuilder($filters);
                $this->selectAll($qb);
                $qb->addOrderBy(static::getAliasedIdProperty(), 'ASC');

                return $qb;
            },
            static::getAliasedIdProperty()
        );

        /** @var SearchResults<T> $results */
        $results = $paginator->execute(
            $page,
            /**
             * @param $results \ArrayIterator<T>
             *
             * @return SearchResults<T>
             */
            function (\ArrayIterator $results, ?PagedResult $pageInfo): SearchResults {
                return new SearchResults($results, $pageInfo);
            }
        );

        return $results;
    }

    public function exists(FilterList $filters): bool
    {
        $qb = $this->getFilteredQueryBuilder($filters);
        $qb->select($qb->expr()->count(static::$alias))
            ->setMaxResults(1);

        return $qb->getQuery()->getSingleScalarResult() > 0;
    }

    public function count(FilterList $filters): int
    {
        $qb = $this->getFilteredQueryBuilder($filters);
        $qb->select($qb->expr()->count(static::$alias));

        return $qb->getQuery()->getSingleScalarResult();
    }

    private function getFilteredQueryBuilder(FilterList $filters): QueryBuilder
    {
        $this->resetAppliedFilters();

        $qb = $this->getEntityManager()->createQueryBuilder()
            ->from(static::$class, static::$alias);

        foreach ($filters->getIterator() as $filter) {
            $this->findAndApplyFilter($qb, $filter);
        }

        return $qb;
    }

    private function findAndApplyFilter(QueryBuilder $qb, Filter $filter): QueryBuilder
    {
        if ($this->isFilterApplied($filter)) {
            return $qb;
        }

        $this->registerAppliedFilter($filter);

        if ($filter instanceof FilterList) {
            foreach ($filter->getIterator() as $childFilter) {
                $this->findAndApplyFilter($qb, $childFilter);
            }

            return $qb;
        }

        return $this->applyFilter($qb, $filter);
    }

    private static function getAliasedIdProperty(): string
    {
        return sprintf('%s.%s', static::$alias, static::$idProperty);
    }

    protected function selectAll(QueryBuilder $qb): QueryBuilder
    {
        return $qb->addSelect(static::$alias);
    }

    private function resetAppliedFilters(): void
    {
        $this->appliedFilters = [];
    }

    private function isFilterApplied(Filter $filter): bool
    {
        return array_key_exists(get_class($filter), $this->appliedFilters);
    }

    private function registerAppliedFilter(Filter $filter): void
    {
        $this->appliedFilters[get_class($filter)] = $filter;
    }
}
