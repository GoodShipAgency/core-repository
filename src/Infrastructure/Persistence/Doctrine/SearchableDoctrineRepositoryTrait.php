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
use Mashbo\CoreRepository\Infrastructure\Persistence\Doctrine\Filter\AliasNameGenerator;
use Mashbo\CoreRepository\Infrastructure\Persistence\Doctrine\Filter\AliasNameGeneratorInterface;
use Mashbo\CoreRepository\Infrastructure\Persistence\Doctrine\Filter\FilterJoinerInterface;
use Mashbo\CoreRepository\Infrastructure\Persistence\Doctrine\Filter\ParameterNameGenerator;
use Mashbo\CoreRepository\Infrastructure\Persistence\Doctrine\Filter\ParameterNameGeneratorInterface;

/**
 * @template T
 */
trait SearchableDoctrineRepositoryTrait
{
    /** @var array<class-string<Filter>, DoctrineFilterHandler|class-string<DoctrineFilterHandler>> */
    private ?array $availableFilters = null;
    private array $appliedFilters = [];
    private static string $idProperty = 'id';
    private static string $alias = 'r';

    private ?ParameterNameGeneratorInterface $parameterNameGenerator = null;
    private ?AliasNameGeneratorInterface $aliasNameGenerator = null;

    protected function applyFilter(QueryBuilder $qb, Filter $filter): QueryBuilder
    {
        throw new \LogicException('Filter of type ' . get_class($filter) . ' is not supported by this repository.');
    }

    abstract protected function getManager(): EntityManagerInterface;

    /** @return class-string<T> */
    protected function getClass(): string
    {
        return static::$class;
    }

    protected static function getAlias(): string
    {
        return static::$alias;
    }

    /**
     * @psalm-suppress MixedReturnTypeCoercion
     *
     * @return SearchResults<T>
     */
    public function search(FilterList $filters, ?LimitOffsetPage $page = null): SearchResults
    {
        $paginator = $this->getPaginatedQueryExecutor($filters);

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

    /** @return \Generator<T> */
    public function batch(FilterList $filterList): \Generator
    {
        $qb = $this->getFilteredQueryBuilder($filterList);
        $qb->addOrderBy(static::getAliasedIdProperty(), 'ASC')
            ->distinct();

        $this->selectAll($qb);

        $query = $qb->getQuery();

        foreach ($query->toIterable() as $entity) {
            yield $entity;

            // This is deprecated but still in docs. Not sure on replacement.
            $this->getManager()->detach($entity);
        }
    }

    public function exists(FilterList $filters): bool
    {
        $qb = $this->getFilteredQueryBuilder($filters);
        $qb->select($qb->expr()->count(static::getAlias()))
            ->setMaxResults(1);

        return $qb->getQuery()->getSingleScalarResult() > 0;
    }

    public function count(FilterList $filters): int
    {
        $qb = $this->getFilteredQueryBuilder($filters);
        $qb->select($qb->expr()->count(static::getAlias()));

        if (empty($qb->getDQLPart('groupBy'))) {
            return (int)$qb->getQuery()->getSingleScalarResult();
        } else {
            $idCountResult = (array)$qb->select(sprintf('COUNT(DISTINCT %s)', static::getAliasedIdProperty()))
                ->resetDQLPart('orderBy')
                ->getQuery()
                ->getScalarResult();

            // The shape of the resulting array is different based on whether the query was grouped and
            // how many rows are returned
            $count = 0;
            array_walk_recursive($idCountResult, function (string $result) use (&$count) {
                $count = $count + (int)$result;
            });

            return $count;
        }
    }

    protected function getFilteredQueryBuilder(FilterList $filters): QueryBuilder
    {
        $this->resetAppliedFilters();

        $qb = $this->getManager()->createQueryBuilder()
            ->from($this->getClass(), static::getAlias());

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

        return $this->invokeFilter($qb, $filter);
    }

    private function invokeFilter(QueryBuilder $queryBuilder, Filter $filter): QueryBuilder
    {
        if ($this->availableFilters === null) {
            $this->availableFilters = $this->configureFilters();
        }

        $handler = $this->getFilterHandler($filter);

        if ($handler !== null) {
            if ($handler instanceof FilterJoinerInterface) {
                if ($handler->hasJoiner()) {
                    $joiner = $handler->getJoiner();

                    $joiner->apply($queryBuilder);
                }
            }

            return $handler->handle($this->getAliasNameGenerator(), $this->getParameterNameGenerator(), $queryBuilder, $filter);
        }

        // Fallback to old method of filter handling
        return $this->applyFilter($queryBuilder, $filter);
    }

    private function getFilterHandler(Filter $filter): ?DoctrineFilterHandler
    {
        $handler = null;

        if (array_key_exists(get_class($filter), $this->availableFilters)) {
            $handler = $this->availableFilters[get_class($filter)];
        } else {
            // Is one of the keys an interface implemented by the filter?
            foreach ($this->availableFilters as $filterClass => $handlerIdentifier) {
                if (is_subclass_of(get_class($filter), $filterClass)) {
                    $handler = $handlerIdentifier;
                    break;
                }
            }
        }

        if ($handler === null) {
            return null;
        }

        if (is_string($handler)) {
            $handler = new $handler();
        }

        if (!$handler instanceof DoctrineFilterHandler) {
            throw new \LogicException('Filter handler for ' . get_class($filter) . ' is not an instance of ' . DoctrineFilterHandler::class);
        }

        return $handler;

    }

    private function configureFilters(): array
    {
        return [];
    }

    private static function getAliasedIdProperty(): string
    {
        return sprintf('%s.%s', static::getAlias(), static::$idProperty);
    }

    protected function selectAll(QueryBuilder $qb): QueryBuilder
    {
        return $qb->addSelect(static::getAlias());
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

    protected function getPaginatedQueryExecutor(FilterList $filters): PaginatedQueryExecutorInterface
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

        return $paginator;
    }

    protected function getParameterNameGenerator(): ParameterNameGeneratorInterface
    {
        if ($this->parameterNameGenerator === null) {
            $this->parameterNameGenerator = new ParameterNameGenerator();
        }

        return $this->parameterNameGenerator;
    }

    protected function getAliasNameGenerator(): AliasNameGeneratorInterface
    {
        if ($this->aliasNameGenerator === null) {
            $this->aliasNameGenerator = new AliasNameGenerator();
        }

        return $this->aliasNameGenerator;
    }
}
