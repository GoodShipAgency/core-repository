<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Domain\Filtering;

interface DateBetweenFilter extends Filter
{
    public function getFrom(): \DateTimeImmutable;

    public function getTo(): \DateTimeImmutable;
}
