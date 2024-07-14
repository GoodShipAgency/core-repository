<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Infrastructure\Persistence\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Mashbo\CoreRepository\Domain\Filtering\Filter;
use Mashbo\CoreRepository\Domain\Filtering\FilterList;
use Mashbo\CoreRepository\Domain\Pagination\LimitOffsetPage;
use Mashbo\CoreRepository\Domain\SearchResults;
use Mashbo\CoreRepository\Infrastructure\Persistence\Doctrine\Filter\AliasNameGenerator;
use Mashbo\CoreRepository\Infrastructure\Persistence\Doctrine\Filter\AliasNameGeneratorInterface;
use Mashbo\CoreRepository\Infrastructure\Persistence\Doctrine\Filter\FilterJoinerInterface;
use Mashbo\CoreRepository\Infrastructure\Persistence\Doctrine\Filter\ParameterNameGenerator;
use Mashbo\CoreRepository\Infrastructure\Persistence\Doctrine\Filter\ParameterNameGeneratorInterface;
use Mashbo\CoreRepository\Infrastructure\Persistence\Doctrine\Pagination\PaginatedQueryExecutorInterface;
use Symfony\Contracts\Service\Attribute\Required;

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

    private ?PaginatedQueryExecutorInterface $paginatedQueryExecutor = null;

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

    #[Required]
    public function setPaginatedQueryExecutor(PaginatedQueryExecutorInterface $paginatedQueryExecutor): void
    {
        $this->paginatedQueryExecutor = $paginatedQueryExecutor;
    }

    /**
     * @psalm-suppress MixedReturnTypeCoercion
     *
     * @return SearchResults<T>
     */
    public function search(FilterList $filters, ?LimitOffsetPage $page = null): SearchResults
    {
        $qb = $this->getFilteredQueryBuilder($filters)
                ->addOrderBy(static::getAliasedIdProperty(), 'ASC');

        $qb = $this->selectAll($qb);

        $paginator = $this->getPaginatedQueryExecutor();

        $results = $paginator->execute($qb, $page);

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
            return (int) $qb->getQuery()->getSingleScalarResult();
        } else {
            $idCountResult = (array) $qb->select(sprintf('COUNT(DISTINCT %s)', static::getAliasedIdProperty()))
                ->resetDQLPart('orderBy')
                ->getQuery()
                ->getScalarResult();

            // The shape of the resulting array is different based on whether the query was grouped and
            // how many rows are returned
            $count = 0;
            array_walk_recursive($idCountResult, function (string $result) use (&$count) {
                $count += (int) $result;
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

        throw new \LogicException('Filter of type '.get_class($filter).' is not supported by this repository.');
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
            throw new \LogicException('Filter handler for '.get_class($filter).' is not an instance of '.DoctrineFilterHandler::class);
        }

        return $handler;
    }

    // You can override this method in repositories to provide a custom paginated query executor on a per-repository basis
    protected function getPaginatedQueryExecutor(): PaginatedQueryExecutorInterface
    {
        if ($this->paginatedQueryExecutor === null) {
            throw new \LogicException('No paginated query executor has been set on this repository. Did you alias PaginatedQueryExecutorInterface in services.yaml?');
        }

        return $this->paginatedQueryExecutor;
    }

    abstract protected function configureFilters(): array;

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
