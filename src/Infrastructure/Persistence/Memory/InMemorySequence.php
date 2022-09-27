<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Infrastructure\Persistence\Memory;

class InMemorySequence
{
    public function __construct(private string $property, private int $nextId = 1)
    {
    }

    public function apply(object $object): int
    {
        $property = $this->getReflectionProperty($object);
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

        throw new \LogicException("The {$this->property} of this ".get_class($object).' object was neither an integer nor null');
    }

    /**
     * @param iterable<int,object> $objects
     *
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

    private function getReflectionProperty(object $object): \ReflectionProperty
    {
        $refl = (new \ReflectionClass($object));

        $property = null;
        do {
            if ($refl->hasProperty($this->property)) {
                $property = $refl->getProperty($this->property);
            } else {
                $refl = $refl->getParentClass();

                if ($refl === false) {
                    throw new \LogicException(sprintf('Class has no %s property and class also has no parent from which it inherits property', $this->property));
                }
            }
        } while ($property === null);

        return $property;
    }
}
