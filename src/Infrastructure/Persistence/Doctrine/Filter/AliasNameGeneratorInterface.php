<?php

namespace Mashbo\CoreRepository\Infrastructure\Persistence\Doctrine\Filter;

interface AliasNameGeneratorInterface
{
    public function generate(): string;
}