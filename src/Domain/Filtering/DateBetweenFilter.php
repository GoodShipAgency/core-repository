<?php

namespace CoreRepository\Domain\Filtering;

interface DateBetweenFilter extends Filter
{
    public function getFrom(): \DateTimeImmutable;
    public function getTo(): \DateTimeImmutable;
}