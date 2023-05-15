<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Application\CQRS\Query;

use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

/**
 * @template T
 * @method T __invoke(Query<T> $query)
 */
interface QueryHandlerInterface
{

}
