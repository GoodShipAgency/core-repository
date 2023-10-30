<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Application\CQRS\Messenger;

use Mashbo\CoreRepository\Application\CQRS\Query\Query;
use Mashbo\CoreRepository\Application\CQRS\Query\QueryBusInterface;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

class MessengerQueryBus implements QueryBusInterface
{
    use HandleTrait {
        handle as handleQuery;
    }

    public function __construct(MessageBusInterface $queryBus)
    {
        $this->messageBus = $queryBus;
    }

    /**
     * @template T
     *
     * @param Query<T> $message
     *
     * @return T
     */
    public function handle(Query $message): mixed
    {
        /** @var T $result */
        $result = $this->handleQuery($message);

        return $result;
    }
}
