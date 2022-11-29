<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Domain\CQRS\Query;

use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

interface QueryHandlerInterface extends MessageHandlerInterface
{
}
