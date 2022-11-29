<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Application\CQRS\Messenger;

use Mashbo\CoreRepository\Domain\CQRS\Command\Command;
use Mashbo\CoreRepository\Domain\CQRS\Command\CommandBusInterface;
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
