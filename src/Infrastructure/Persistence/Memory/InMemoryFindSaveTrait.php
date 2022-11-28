<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Infrastructure\Persistence\Memory;

use Mashbo\CoreRepository\Infrastructure\Persistence\IdManagementTrait;

/**
 * @template TId
 * @template T of object
 */
trait InMemoryFindSaveTrait
{
    use IdManagementTrait;

    /** @var array<string, T> */
    protected array $records = [];

    /** @return class-string<T> */
    abstract protected function getClass(): string;

    /**
     * @param TId $id
     *
     * @return T
     */
    public function find(mixed $id): object
    {
        if (is_array($id)) {
            $idProperties = $this->getIdProperties(new \ReflectionClass($this->getClass()));
            $key = '';
            foreach ($idProperties as $property) {
                $idPropertyName = (string) $property->name;

                assert(array_key_exists($idPropertyName, $id));

                $value = $id[$idPropertyName];

                if ($this->isEntity($value)) {
                    assert(is_object($value));
                    /**
                     * @psalm-suppress MixedAssignment
                     */
                    $value = $this->getId($value);
                }
                $key .= (string) $value.'___';
            }
        } else {
            if ($this->isEntity($id)) {
                assert(is_object($id));
                /**
                 * @psalm-suppress MixedAssignment
                 */
                $id = $this->getId($id);
            }

            $key = (string) $id.'___';
        }

        $record = $this->records[$key] ?? null;

        if ($record === null) {
            throw new \LogicException('Not found');
        }

        return $record;
    }

    /**
     * @param T $record
     */
    public function save(object $record): void
    {
        $idProperties = $this->getIdProperties(new \ReflectionClass($record));

        $key = '';

        foreach ($idProperties as $property) {
            /** @psalm-suppress MixedAssignment */
            $value = $property->getValue($record);

            if ($this->isEntity($value)) {
                assert(is_object($value));
                /**
                 * @psalm-suppress MixedAssignment
                 */
                $value = $this->getId($value);
            }
            $key .= (string) $value.'___';
        }
        $this->records[$key] = $record;
    }

    /** @return T[] */
    public function all(): array
    {
        return $this->records;
    }
}
