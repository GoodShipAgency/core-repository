<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Infrastructure\Persistence\Doctrine\Filter;

interface ParameterNameGeneratorInterface
{
    public function generate(): string;
}
