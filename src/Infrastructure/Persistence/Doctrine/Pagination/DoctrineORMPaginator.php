<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Infrastructure\Persistence\Doctrine\Pagination;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Mashbo\CoreRepository\Domain\Pagination\LimitOffsetPage;
use Mashbo\CoreRepository\Domain\Pagination\PagedResult;
use Mashbo\CoreRepository\Domain\SearchResults;

class DoctrineORMPaginator implements PaginatedQueryExecutorInterface
{
    public function __construct(
        private bool $fetchJoinCollection = false
    ) {
    }

    public function execute(QueryBuilder $queryBuilder, ?LimitOffsetPage $page): SearchResults
    {
        if ($page === null) {
            /**
             * @var \Traversable $result
             *                   This isn't always a \Traversable, but it's what ArrayIterator expects and it works regardless
             */
            $result = $queryBuilder->getQuery()->getResult();

            return new SearchResults(new \ArrayIterator(iterator_to_array($result)), null);
        }

        $queryBuilder->setFirstResult(($page->getPageNumber() - 1) * $page->getLimit())
            ->setMaxResults($page->getLimit());

        $paginator = new Paginator($queryBuilder->getQuery(), fetchJoinCollection: $this->fetchJoinCollection);

        $results = new \ArrayIterator(iterator_to_array($paginator));
        $pageInfo = new PagedResult($page, $paginator->count(), $results->count());

        return new SearchResults($results, $pageInfo);
    }
}
