<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Application\CQRS;

use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

interface QueryHandlerInterface extends MessageHandlerInterface
{
}
