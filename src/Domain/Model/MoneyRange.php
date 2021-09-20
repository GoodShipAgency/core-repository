<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Domain\Model;

use Money\Money;

/**
 * @psalm-immutable
 */
class MoneyRange
{
    public function __construct(private ?Money $min, private ?Money $max)
    {
        if ($min !== null && $max !== null) {
            if (!$min->getCurrency()->equals($max->getCurrency())) {
                throw new \LogicException("Both min and max should be in the same currency");
            }
        }

        if ($min === null && $max === null) {
            throw new \LogicException("A money range must either have a min or a max");
        }
    }

    public function getMin(): ?Money
    {
        return $this->min;
    }

    public function getMax(): ?Money
    {
        return $this->max;
    }
}
