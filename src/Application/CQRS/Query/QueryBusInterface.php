<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Application\CQRS\Query;

interface QueryBusInterface
{
    public function handle(Query $message): mixed;
}
