<?php

namespace Mashbo\CoreRepository\Infrastructure\Persistence\Doctrine\Filter;

interface ParameterNameGeneratorInterface
{
    public function generate(): string;
}