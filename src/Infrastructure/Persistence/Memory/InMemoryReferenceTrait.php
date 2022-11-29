<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Infrastructure\Persistence\Memory;

use Mashbo\CoreRepository\Infrastructure\Persistence\IdManagementTrait;

/**
 * @template TId
 * @template T
 */
trait InMemoryReferenceTrait
{
    use IdManagementTrait;

    /** @var array<string, T> */
    protected array $referenceCache = [];

    /** @return class-string<T> */
    abstract protected function getClass(): string;

    /**
     * @param TId $id
     *
     * @return T
     */
    public function reference(mixed $id): object
    {
        $key = (string) $id;

        if (array_key_exists($key, $this->referenceCache)) {
            return $this->referenceCache[$key];
        }

        $reflection = new \ReflectionClass($this->getClass());

        $record = $reflection->newInstanceWithoutConstructor();
        $this->getIdProperty($reflection)->setValue($record, $id);

        $this->referenceCache[$key] = $record;

        return $record;
    }
}
