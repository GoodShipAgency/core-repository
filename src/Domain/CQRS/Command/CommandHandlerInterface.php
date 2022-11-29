<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Domain\CQRS\Command;

use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

interface CommandHandlerInterface extends MessageHandlerInterface
{
}
