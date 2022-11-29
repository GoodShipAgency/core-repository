<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Model;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterval;
use Mashbo\CoreRepository\Domain\Model\CarbonDateRange;
use PHPUnit\Framework\TestCase;

class CarbonDateRangeTest extends TestCase
{
    public function test_date_range_from_cannot_be_after_to(): void
    {
        self::expectException(\InvalidArgumentException::class);
        self::expectExceptionMessage("Date range 'from' cannot be after 'to'");
        new CarbonDateRange(
            new CarbonImmutable('1900-02-01 00:00:00'),
            new CarbonImmutable('1900-01-02 00:00:00'),
        );
    }

    public function test_date_range_is_correct(): void
    {
        $sut = new CarbonDateRange(
            new CarbonImmutable('1900-01-01 00:00:00'),
            new CarbonImmutable('1900-01-02 00:00:00'),
        );

        self::assertEquals(new CarbonImmutable('1900-01-01 00:00:00'), $sut->getFrom());
        self::assertEquals(new CarbonImmutable('1900-01-02 00:00:00'), $sut->getTo());
    }

    public function test_number_of_days_in_range(): void
    {
        $sut = new CarbonDateRange(
            new CarbonImmutable('1900-01-01 00:00:00'),
            new CarbonImmutable('1900-01-02 00:00:00'),
        );
        self::assertEquals(2, $sut->getTotalDays());

        $sut = $sut->excludeEndDate();
        self::assertEquals(1, $sut->getTotalDays());

        $sut = new CarbonDateRange(
            new CarbonImmutable('1900-01-01 00:00:00'),
            new CarbonImmutable('1900-01-10 00:00:00'),
        );
        self::assertEquals(10, $sut->getTotalDays());

        $sut = $sut->excludeEndDate();
        self::assertEquals(9, $sut->getTotalDays());

        $sut = new CarbonDateRange(
            new CarbonImmutable('1900-01-01 00:00:00'),
            new CarbonImmutable('1900-02-01 00:00:00'),
        );
        self::assertEquals(32, $sut->getTotalDays());

        $sut = $sut->excludeEndDate();
        self::assertEquals(31, $sut->getTotalDays());

        $sut = new CarbonDateRange(
            new CarbonImmutable('1900-01-01 00:00:00'),
            new CarbonImmutable('1900-12-31 00:00:00'),
        );
        self::assertEquals(365, $sut->getTotalDays());

        $sut = $sut->excludeEndDate();
        self::assertEquals(364, $sut->getTotalDays());

        $sut = new CarbonDateRange(
            new CarbonImmutable('1900-01-01 00:00:00'),
            new CarbonImmutable('1901-01-01 00:00:00'),
        );
        self::assertEquals(366, $sut->getTotalDays());

        $sut = $sut->excludeEndDate();
        self::assertEquals(365, $sut->getTotalDays());
    }

    public function test_number_of_leap_days_in_range(): void
    {
        $sut = new CarbonDateRange(
            new CarbonImmutable('1900-01-01 00:00:00'),
            new CarbonImmutable('1900-02-01 00:00:00'),
        );
        self::assertEquals(0, $sut->getTotalLeapDays());
        self::assertEquals(32, $sut->getTotalDays());

        $sut = new CarbonDateRange(
            new CarbonImmutable('1900-01-01 00:00:00'),
            new CarbonImmutable('1900-03-01 00:00:00'),
        );
        self::assertEquals(0, $sut->getTotalLeapDays());
        self::assertEquals(60, $sut->getTotalDays());

        $sut = new CarbonDateRange(
            new CarbonImmutable('1904-01-01 00:00:00'),
            new CarbonImmutable('1904-03-01 00:00:00'),
        );
        self::assertEquals(1, $sut->getTotalLeapDays());
        self::assertEquals(61, $sut->getTotalDays());

        $sut = $sut->excludeLeapDays();
        self::assertEquals(60, $sut->getTotalDays());
    }

    public function test_date_range_interval(): void
    {
        $sut = new CarbonDateRange(
            new CarbonImmutable('1900-01-01 00:00:00'),
            new CarbonImmutable('1901-01-01 00:00:00'),
        );

        self::assertEquals(366, $sut->getTotalDays());

        $interval = $sut->getInterval();
        self::assertEquals('1 year', $interval->forHumans());
    }

    public function test_date_range_period(): void
    {
        $sut = new CarbonDateRange(
            new CarbonImmutable('1900-01-01 00:00:00'),
            new CarbonImmutable('1901-01-01 00:00:00'),
        );

        $period = $sut->getPeriod(CarbonInterval::months(1));
        self::assertEquals(13, $period->count());

        $sut = $sut->excludeEndDate();
        $period = $sut->getPeriod(CarbonInterval::months(1));
        self::assertEquals(12, $period->count());
    }

    public function test_date_range_intersection(): void
    {
        $sut = new CarbonDateRange(
            new CarbonImmutable('1900-01-01 00:00:00'),
            new CarbonImmutable('1901-01-01 00:00:00'),
        );

        $intersectionDateRange = new CarbonDateRange(
            new CarbonImmutable('1901-01-01 00:00:00'),
            new CarbonImmutable('1902-01-01 00:00:00'),
        );
        self::assertFalse($sut->intersects($intersectionDateRange));

        $intersectionDateRange = new CarbonDateRange(
            new CarbonImmutable('1900-12-31 23:59:59'),
            new CarbonImmutable('1902-01-01 00:00:00'),
        );
        self::assertTrue($sut->intersects($intersectionDateRange));

        $intersectionDateRange = new CarbonDateRange(
            new CarbonImmutable('1899-01-01 00:00:00'),
            new CarbonImmutable('1900-01-01 00:00:00'),
        );
        self::assertFalse($sut->intersects($intersectionDateRange));

        $intersectionDateRange = new CarbonDateRange(
            new CarbonImmutable('1900-02-01 00:00:00'),
            new CarbonImmutable('1900-03-01 00:00:00'),
        );
        self::assertTrue($sut->intersects($intersectionDateRange));
    }

    public function test_can_exclude_end_date_and_leap_days_using_fluent_interface(): void
    {
        $sut = new CarbonDateRange(
            new CarbonImmutable('2022-04-04'),
            new CarbonImmutable('2022-04-04')
        );

        self::assertTrue($sut->isEndDateIncluded());
        self::assertTrue($sut->isLeapDaysIncluded());

        $sutEndDateExcluded = $sut->excludeEndDate();
        self::assertFalse($sutEndDateExcluded->isEndDateIncluded());
        self::assertTrue($sutEndDateExcluded->isLeapDaysIncluded());

        $sutLeapDaysExcluded = $sut->excludeLeapDays();
        self::assertTrue($sutLeapDaysExcluded->isEndDateIncluded());
        self::assertFalse($sutLeapDaysExcluded->isLeapDaysIncluded());

        $sutBothExcluded = $sut->excludeEndDate()->excludeLeapDays();
        self::assertFalse($sutBothExcluded->isEndDateIncluded());
        self::assertFalse($sutBothExcluded->isLeapDaysIncluded());
    }

    public function test_date_in_date_range(): void
    {
        // 1 day range
        // No times provided
        $sut = new CarbonDateRange(
            new CarbonImmutable('2022-04-04'),
            new CarbonImmutable('2022-04-04')
        );

        $date = new CarbonImmutable('2022-04-03');
        $this->assertFalse($sut->isDateInRange($date));

        $date = new CarbonImmutable('2022-04-04');
        $this->assertTrue($sut->isDateInRange($date));

        // 1 day range
        // Times provided
        $sut = new CarbonDateRange(
            new CarbonImmutable('2022-04-04 12:20'),
            new CarbonImmutable('2022-04-04 19:59')
        );

        $date = new CarbonImmutable('2022-04-03');
        $this->assertFalse($sut->isDateInRange($date));

        // This will result in 2022-04-04 00:00:00
        $date = new CarbonImmutable('2022-04-04');
        $this->assertFalse($sut->isDateInRange($date));

        $date = new CarbonImmutable('2022-04-04 13:50');
        $this->assertTrue($sut->isDateInRange($date));

        // >1 day range
        // Times provided
        $sut = new CarbonDateRange(
            new CarbonImmutable('2022-04-04 12:20'),
            new CarbonImmutable('2022-04-08 19:59')
        );

        $date = new CarbonImmutable('2022-04-03');
        $this->assertFalse($sut->isDateInRange($date));

        $date = new CarbonImmutable('2022-04-09');
        $this->assertFalse($sut->isDateInRange($date));

        $date = new CarbonImmutable('2022-04-04 12:20');
        $this->assertTrue($sut->isDateInRange($date));

        $date = new CarbonImmutable('2022-04-04 13:50');
        $this->assertTrue($sut->isDateInRange($date));

        $date = new CarbonImmutable('2022-04-08 19:59');
        $this->assertTrue($sut->isDateInRange($date));
    }
}
