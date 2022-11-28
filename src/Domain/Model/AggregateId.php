<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Domain\Model;

use Symfony\Component\Uid\Uuid;

/** @psalm-consistent-constructor  */
abstract class AggregateId extends Uuid
{
    public function __construct(string $uuid)
    {
        parent::__construct($uuid);
    }

    public static function create(): static
    {
        return new static(Uuid::v4()->toRfc4122());
    }

    public function toString(): string
    {
        return $this->toRfc4122();
    }
}
