<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Infrastructure\Persistence\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Mashbo\CoreRepository\Domain\Exception\NoSuchRecordException;

/**
 * @template TId
 * @template T of object
 */
trait DoctrineFindSaveTrait
{
    abstract protected function getManager(): EntityManagerInterface;

    /** @return class-string<T> */
    abstract protected function getClass(): string;

    /** @param T $record */
    public function save(object $record): void
    {
        $this->getManager()->persist($record);
        $this->getManager()->flush();
    }

    /**
     * @param TId $id
     *
     * @return T
     *
     */
    public function find(mixed $id): object
    {
        $record = $this->getManager()->find($this->getClass(), $id);

        if ($record === null) {
            throw $this->createNotFoundException($id);
        }

        return $record;
    }

    /**
     * @param TId $id
     *
     * @return T
     */
    public function reference(mixed $id): object
    {
        return $this->getManager()->getReference($this->getClass(), $id) ?? throw new \LogicException('Reference returned null');
    }

    protected function createNotFoundException(mixed $id): \Exception
    {
        return new NoSuchRecordException(
            sprintf('No "%s" record found with id "%s"', $this->getClass(), $id)
        );
    }
}
