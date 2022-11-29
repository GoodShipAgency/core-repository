<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Domain\CQRS\Command;

interface CommandBusInterface
{
    public function dispatch(Command $command): void;
}
