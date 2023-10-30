<?php

namespace Mashbo\CoreRepository\Infrastructure\Persistence\Doctrine\Filter;

use Mashbo\CoreRepository\Infrastructure\Persistence\Doctrine\Joiner\JoinerInterface;

trait FilterJoinerTrait
{
    private ?JoinerInterface $joiner = null;

    public function withJoiner(JoinerInterface $joiner): self
    {
        $this->joiner = $joiner;

        return $this;
    }

    public function getJoiner(): JoinerInterface
    {
        if (null === $this->joiner) {
            throw new \RuntimeException('Joiner not set');
        }

        return $this->joiner;
    }


    public function hasJoiner(): bool
    {
        return null !== $this->joiner;
    }
}