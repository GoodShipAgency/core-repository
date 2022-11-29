<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Domain\CQRS\Query;

interface QueryBusInterface
{
    public function handle(Query $message): mixed;
}
