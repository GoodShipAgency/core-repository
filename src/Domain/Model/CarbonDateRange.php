<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Domain\Model;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterval;
use Carbon\CarbonPeriod;

/**
 * @psalm-immutable
 *
 * @psalm-suppress ImpureMethodCall
 */
class CarbonDateRange
{
    private bool $includeEndDate = true;
    private bool $includeLeapDays = true;

    public function __construct(
        private readonly CarbonImmutable $from,
        private readonly CarbonImmutable $to,

        bool $includeEndDate = true,
        bool $includeLeapDays = true,
    ) {
        if ($from > $to) {
            throw new \InvalidArgumentException("Date range 'from' cannot be after 'to'");
        }

        $this->includeEndDate = $includeEndDate;
        $this->includeLeapDays = $includeLeapDays;
    }

    public function getFrom(): CarbonImmutable
    {
        return $this->from;
    }

    public function getTo(): CarbonImmutable
    {
        return $this->to;
    }

    public function excludeEndDate(): self
    {
        return new self($this->getFrom(), $this->getTo(), includeEndDate: false, includeLeapDays: $this->isLeapDaysIncluded());
    }

    public function isEndDateIncluded(): bool
    {
        return $this->includeEndDate;
    }

    public function isEndDateExcluded(): bool
    {
        return !$this->includeEndDate;
    }

    public function excludeLeapDays(): self
    {
        return new self($this->getFrom(), $this->getTo(), includeEndDate: $this->isEndDateIncluded(), includeLeapDays: false);
    }

    public function isLeapDaysIncluded(): bool
    {
        return $this->includeLeapDays;
    }

    public function isLeapDaysExcluded(): bool
    {
        return !$this->includeLeapDays;
    }

    public function getPeriod(CarbonInterval $interval): CarbonPeriod
    {
        /** @psalm-suppress ImpureMethodCall */
        $period = $interval->toPeriod($this->getFrom(), $this->getTo());

        return $this->resolvePeriod($period);
    }

    public function getTotalDays(): int
    {
        $totalDays = (int) ceil($this->getFrom()->diffInDays($this->getTo()));

        if ($this->isEndDateIncluded()) {
            ++$totalDays;
        }

        if ($this->isLeapDaysIncluded()) {
            return $totalDays;
        }

        return $totalDays - $this->getTotalLeapDays();
    }

    public function getTotalLeapDays(): int
    {
        $period = new CarbonPeriod(
            $this->getFrom(),
            CarbonInterval::years(1),
            $this->getTo()
        );
        $period = $this->resolvePeriod($period);

        $numberOfLeapYearsInDateRange = 0;
        foreach ($period as $date) {
            /** @var CarbonImmutable $date */
            $leapDay = new CarbonImmutable(sprintf('%s-02-29', $date->format('Y')));

            if ($leapDay->isLeapYear() && $leapDay->isBetween($this->getFrom(), $this->getTo())) {
                ++$numberOfLeapYearsInDateRange;
            }
        }

        return $numberOfLeapYearsInDateRange;
    }

    public function getInterval(): CarbonInterval
    {
        return $this->getFrom()->diffAsCarbonInterval($this->getTo());
    }

    public function intersects(self $b): bool
    {
        return max($this->getFrom(), $b->getFrom()) < min($this->getTo(), $b->getTo());
    }

    public function isDateInRange(CarbonImmutable $date): bool
    {
        return $date->greaterThanOrEqualTo($this->getFrom())
            && $date->lessThanOrEqualTo($this->getTo());
    }

    private function resolvePeriod(CarbonPeriod $period): CarbonPeriod
    {
        $period = $period->toggleOptions(CarbonPeriod::EXCLUDE_END_DATE, $this->isEndDateExcluded());
        $period = $period->toggleOptions(CarbonPeriod::IMMUTABLE, true);

        return $period;
    }
}
