<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Application\CQRS;

interface QueryBusInterface
{
    public function handle(Query $message): mixed;
}
