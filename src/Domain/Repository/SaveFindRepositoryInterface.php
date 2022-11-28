<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Domain\Repository;

/**
 * @template TId
 * @template T
 *
 * @extends SaveRepositoryInterface<T>
 */
interface SaveFindRepositoryInterface extends SaveRepositoryInterface
{
    /**
     * @param TId $id
     *
     * @return T
     */
    public function find(mixed $id): object;
}
