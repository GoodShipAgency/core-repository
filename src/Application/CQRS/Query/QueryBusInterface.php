<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Application\CQRS\Query;

interface QueryBusInterface
{
    /**
     * @template T
     *
     * @param Query<T> $message
     *
     * @return T
     */
    public function handle(Query $message): mixed;
}
