<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Infrastructure\Persistence\Doctrine\Filter;

class AliasNameGenerator implements AliasNameGeneratorInterface
{
    private int $aliasCounter = 0;

    public function generate(): string
    {
        return 'ma'.$this->aliasCounter++;
    }
}
