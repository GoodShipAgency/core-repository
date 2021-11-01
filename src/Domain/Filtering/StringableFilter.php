<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Domain\Filtering;

interface StringableFilter
{
    public function getName(): string;

    public function getValue(): string;
}
