<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Infrastructure\Persistence\Doctrine;

use Doctrine\ORM\EntityManagerInterface;

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
     */
    public function find(mixed $id): object
    {
        if (is_array($id)) {
            /**
             * Some read entities, e.g. Contract, pass two identifiers to find methods, but only one of them is actually
             * registered as an Id in doctrine, so we should only pass that one to the manager.
             * The reason that particular entity has two identifiers but only uses one is that SearchableDoctrineTrait does not
             * yet support compound keys, and a single Uuid is enough to uniquely identify it, even if it isn't 100% "correct".
             */
            $entityIdentifiers = $this->getManager()->getClassMetadata($this->getClass())->getIdentifierFieldNames();

            foreach ($id as $identifierFieldName => $_identifierValue) {
                if (!in_array($identifierFieldName, $entityIdentifiers, true)) {
                    unset($id[$identifierFieldName]);
                }
            }
        }

        $record = $this->getManager()->find($this->getClass(), $id);

        if ($record === null) {
            throw new \LogicException('TODO: Add ability to override which exception is thrown');
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
}
