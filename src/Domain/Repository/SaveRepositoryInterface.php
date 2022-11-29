<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Domain\Repository;

/** @template T */
interface SaveRepositoryInterface
{
    /** @param T $record */
    public function save(object $record): void;
}
