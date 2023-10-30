To Upgrade to 6.*, you must change all references of:

`protected function reportQueryBuilder(QueryBuilder $qb, Report $report): QueryBuilder;`

to 

`protected function reportQueryBuilder(QueryBuilder $qb, ReportInterface $report): QueryBuilder;`

To Upgrade to 7.*:

- You must modify all usages of DoctrineSearchableTrait to implement the abstract method `configureFilters`. You can use the CallbackFilterHandler as a stop gap to use the existing `applyFilters` method. 
```
protected function configureFilters(): array
{
    return [Filter::class => (new CallbackFilterHandler($this->applyFilter(...)))];
}
```

To Upgrade to 8.*

- LegacyPaginatedQueryExecutor no longer supports tables where the primary key is not called `id`. Either implement a different paginator or rename your primary key to `id`.
- Add the following alias to your services configuration:
```
    Mashbo\CoreRepository\Infrastructure\Persistence\Doctrine\Pagination\PaginatedQueryExecutorInterface:
        class: Mashbo\CoreRepository\Infrastructure\Persistence\Doctrine\Pagination\LegacyPaginatedQueryExecutor
```

- You'll need to update your Doctrine repository tests to receive a paginator. Consider adding a method such as this to `DatabaseTestTrait`:
```
/**
     * @template T of AbstractDoctrineRepository
     *
     * @param class-string<T> $repositoryName
     *
     * @return T
     */
    protected function instantiate(string $repositoryName): object
    {
        /** @psalm-suppress UnsafeInstantiation */
        $repository = new $repositoryName($this->getManager());

        if (method_exists($repository, 'setPaginatedQueryExecutor')) {
            /** @psalm-suppress PossiblyUndefinedMethod */
            $repository->setPaginatedQueryExecutor(static::getContainer()->get(PaginatedQueryExecutorInterface::class));
        }

        return $repository;
    }
```

  Then using it in your tests like so:
  ```
   protected function getRepositoryUnderTest(): DoctrineLandlordStatementRepository
    {
        return $this->instantiate(DoctrineLandlordStatementRepository::class);
    }
  ```
   You can use the following regex to replace globally:
   ```
   return new (Doctrine[A-Za-z]+Repository)\(\$this->getManager\(\)\);
   ```