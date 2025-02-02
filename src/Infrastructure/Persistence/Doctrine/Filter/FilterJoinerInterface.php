<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Infrastructure\Persistence\Doctrine\Filter;

use Mashbo\CoreRepository\Infrastructure\Persistence\Doctrine\Joiner\JoinerInterface;

interface FilterJoinerInterface
{
    public function withJoiner(JoinerInterface $joiner): self;

    public function getJoiner(): JoinerInterface;

    public function hasJoiner(): bool;
}
