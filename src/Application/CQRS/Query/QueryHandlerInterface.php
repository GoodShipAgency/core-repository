<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Application\CQRS\Query;

use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

/** @template T */
interface QueryHandlerInterface
{
    /**
     * @param Query<T> $query
     * @return T
     */
    public function __invoke(Query $query): mixed;
}
