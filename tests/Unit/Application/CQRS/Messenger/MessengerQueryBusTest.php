<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\CQRS\Messenger;

use Mashbo\CoreRepository\Application\CQRS\Messenger\MessengerQueryBus;
use Mashbo\CoreRepository\Domain\CQRS\Query\Query;
use Mashbo\CoreRepository\Domain\SearchResults;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

class MessengerQueryBusTest extends TestCase
{
    public function test_can_dispatch_query(): void
    {
        $queryBus = $this->createMock(MessageBusInterface::class);
        $sut = new MessengerQueryBus($queryBus);

        $query = $this->createMock(Query::class);

        $result = new SearchResults(new \ArrayIterator(), null);
        $envelope = new Envelope($query, [new HandledStamp($result, 'handler')]);

        $queryBus->expects($this->once())
            ->method('dispatch')
            ->with($query)
            ->willReturn($envelope);

        $returnedResult = $sut->handle($query);

        $this->assertSame($result, $returnedResult);
    }
}
