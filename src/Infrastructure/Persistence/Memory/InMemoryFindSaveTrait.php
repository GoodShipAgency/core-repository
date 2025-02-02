<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Infrastructure\Persistence\Memory;

use Mashbo\CoreRepository\Domain\Exception\NoSuchRecordException;
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
        $id = (string) $id;

        $record = $this->records[$id] ?? null;

        if ($record === null) {
            throw $this->createNotFoundException($id);
        }

        return $record;
    }

    /**
     * @param T $record
     */
    public function save(object $record): void
    {
        $idProperties = $this->getIdProperties(new \ReflectionClass($record));

        $key = (string) $this->getId($record);

        $this->records[$key] = $record;
    }

    /** @return T[] */
    public function all(): array
    {
        return $this->records;
    }

    protected function createNotFoundException(mixed $id): \Exception
    {
        return new NoSuchRecordException(
            sprintf('No "%s" record found with id "%s"', $this->getClass(), $id)
        );
    }
}
