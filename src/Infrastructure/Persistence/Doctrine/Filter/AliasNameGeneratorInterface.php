<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Infrastructure\Persistence\Doctrine\Filter;

interface AliasNameGeneratorInterface
{
    public function generate(): string;
}
