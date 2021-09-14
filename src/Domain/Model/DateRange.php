<?php

declare(strict_types=1);

namespace CoreRepository\Domain\Model;

/**
 * @psalm-immutable
 */
class DateRange
{
    public function __construct(private \DateTimeImmutable $from, private \DateTimeImmutable $to) {}

    public function getFrom(): \DateTimeImmutable
    {
        return $this->from;
    }

    public function getTo(): \DateTimeImmutable
    {
        return $this->to;
    }
}
