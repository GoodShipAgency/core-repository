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