<?php

namespace Mashbo\CoreRepository\Infrastructure\Persistence\Doctrine;

class ParameterNameGenerator implements ParameterNameGeneratorInterface
{
    private int $parameterCounter = 0;

    public function generate(): string
    {
        return 'p' . $this->parameterCounter++;
    }
}