<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Infrastructure\Persistence;

use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;

/**
 * @template TId
 * @template T of object
 */
trait IdManagementTrait
{
    /**
     * @psalm-suppress MixedInferredReturnType
     *
     * @param T $record
     *
     * @return TId
     */
    protected function getId(object $record): mixed
    {
        $refl = new \ReflectionClass($record);

        $properties = $this->getIdProperties($refl);

        if (count($properties) === 1) {
            return reset($properties)->getValue($record);
        } elseif (count($properties) > 1) {
            throw new \LogicException('Multiple Ids not supported');
        }

        throw new \Exception('No Id on model');
    }


    protected function getIdProperty(\ReflectionClass $refl): \ReflectionProperty
    {
        foreach ($refl->getProperties() as $property) {
            if (count($property->getAttributes(Id::class)) > 0) {
                return $property;
            }
        }

        throw new \Exception('No Id on model');
    }

    /** @return \ReflectionProperty[] */
    protected function getIdProperties(\ReflectionClass $refl): array
    {
        return array_filter(
            $refl->getProperties(),
            fn(\ReflectionProperty $property): bool => count($property->getAttributes(Id::class)) > 0
        );
    }

    protected function isEntity(mixed $property): bool
    {
        if (!is_object($property)) {
            return false;
        }

        $refl = new \ReflectionClass($property);

        if (count($refl->getAttributes(Entity::class))) {
            return true;
        }

        return false;
    }
}
