<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Domain\Repository;

/** @template T */
interface RemovableRepositoryInterface
{
    /** @param T $record */
    public function remove(object $record): void;
}
