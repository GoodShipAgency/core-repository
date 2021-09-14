<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Domain\Filtering;

trait DateBetweenFilterTrait
{
    public function __construct(
        private \DateTimeImmutable $from,
        private \DateTimeImmutable $to
    ) {
    }

    public function getFrom(): \DateTimeImmutable
    {
        return $this->from;
    }

    public function getTo(): \DateTimeImmutable
    {
        return $this->to;
    }
}
