<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Infrastructure\Persistance\InMemory;

class InMemorySequence
{
    public function __construct(private string $property, private int $nextId = 1) {}

    public function apply(object $object): int
    {
        $property = (new \ReflectionClass($object))->getProperty($this->property);
        $property->setAccessible(true);

        /** @var ?int|mixed $id */
        $id = $property->getValue($object);

        if (is_int($id)) {
            return $id;
        }

        if ($id === null) {
            $id = $this->nextId++;
            $property->setValue($object, $id);

            return $id;
        }

        throw new \LogicException(
            "The {$this->property} of this " . get_class($object) . " object was neither an integer nor null"
        );
    }

    /**
     * @param iterable<int,object> $objects
     * @return array<int,int>
     */
    public function applyAll(iterable $objects): array
    {
        $ids = [];
        foreach ($objects as $object) {
            $ids[] = $this->apply($object);
        }
        return $ids;
    }
}
