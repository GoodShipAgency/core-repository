<?php

namespace Mashbo\CoreRepository\Infrastructure\Persistence\Doctrine;

interface AliasNameGeneratorInterface
{
    public function generate(): string;
}