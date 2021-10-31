<?php

namespace Mashbo\CoreRepository\Domain\Filtering;

interface StringableFilter
{
    public function getName(): string;

    public function getValue(): string;
}
