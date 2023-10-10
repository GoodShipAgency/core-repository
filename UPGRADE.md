To Upgrade to 6.*, you must change all references of:

`protected function reportQueryBuilder(QueryBuilder $qb, Report $report): QueryBuilder;`

to 

`protected function reportQueryBuilder(QueryBuilder $qb, ReportInterface $report): QueryBuilder;`