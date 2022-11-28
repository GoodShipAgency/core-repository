<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Application\CQRS;

interface CommandBusInterface
{
    public function dispatch(Command $command): void;
}
