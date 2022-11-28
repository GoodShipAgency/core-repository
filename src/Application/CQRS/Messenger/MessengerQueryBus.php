<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Application\CQRS\Messenger;

use Mashbo\CoreRepository\Application\CQRS\Query;
use Mashbo\CoreRepository\Application\CQRS\QueryBusInterface;
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

    public function handle(Query $message): mixed
    {
        return $this->handleQuery($message);
    }
}
