<?php

namespace Mashbo\CoreRepository\Infrastructure\Persistence\Doctrine;

interface ParameterNameGeneratorInterface
{
    public function generate(): string;
}