<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Infrastructure\Persistence\Memory;

/**
 * @mixin  InMemoryFindSaveTrait
 *
 * @template T
 */
trait InMemoryRemovableTrait
{
    public function remove(object $record): void
    {
        foreach ($this->records as $key => $existingRecord) {
            if ($existingRecord === $record) {
                unset($this->records[$key]);
            }
        }
    }
}
