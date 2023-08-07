<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Domain\Repository;

/** @template T of object */
interface SaveRepositoryInterface
{
    /** @param T $record */
    public function save(mixed $record): void;
}
