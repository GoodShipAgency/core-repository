<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Application\CQRS\Messenger;

use Mashbo\CoreRepository\Application\CQRS\Command;
use Mashbo\CoreRepository\Application\CQRS\CommandBusInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class MessengerCommandBus implements CommandBusInterface
{
    public function __construct(private readonly MessageBusInterface $commandBus)
    {
    }

    public function dispatch(Command $command): void
    {
        $this->commandBus->dispatch($command);
    }
}
