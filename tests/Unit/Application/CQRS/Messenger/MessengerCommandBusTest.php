<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\CQRS\Messenger;

use Mashbo\CoreRepository\Application\CQRS\Messenger\MessengerCommandBus;
use Mashbo\CoreRepository\Domain\CQRS\Command\Command;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

class MessengerCommandBusTest extends TestCase
{
    public function test_can_dispatch_command(): void
    {
        $commandBus = $this->createMock(MessageBusInterface::class);
        $sut = new MessengerCommandBus($commandBus);

        $command = $this->createMock(Command::class);
        $envelope = new Envelope($command);

        $commandBus->expects($this->once())
            ->method('dispatch')
            ->with($command)
            ->willReturn($envelope);

        $sut->dispatch($command);
    }
}
