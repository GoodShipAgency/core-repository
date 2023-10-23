<?php

namespace Mashbo\CoreRepository\Infrastructure\Persistence\Doctrine;

class AliasNameGenerator implements AliasNameGeneratorInterface
{
    private int $aliasCounter = 0;

    public function generate(): string
    {
        return 'a' . $this->aliasCounter++;
    }
}