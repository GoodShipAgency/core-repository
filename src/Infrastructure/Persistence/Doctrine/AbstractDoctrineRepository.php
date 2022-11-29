<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Infrastructure\Persistence\Doctrine;

use Doctrine\ORM\EntityManagerInterface;

/**
 * @template TId
 * @template T of object
 */
abstract class AbstractDoctrineRepository
{
    /** @use DoctrineFindSaveTrait<TId, T> */
    use DoctrineFindSaveTrait;

    protected static string $class;

    /** @return class-string<T> */
    abstract protected function getClass(): string;

    private EntityManagerInterface $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
        static::$class = $this->getClass();
    }

    protected function getManager(): EntityManagerInterface
    {
        return $this->manager;
    }
}
