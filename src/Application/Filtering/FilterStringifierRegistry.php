<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Application\Filtering;

use Mashbo\CoreRepository\Application\Filtering\Exception\FilterStringifierKeyAlreadyInUseException;
use Mashbo\CoreRepository\Application\Filtering\Exception\FilterStringifierNotFoundByKeyException;
use Mashbo\CoreRepository\Application\Filtering\Exception\FilterStringifierNotFoundException;
use Mashbo\CoreRepository\Application\Filtering\Stringifiers\Stringifier;
use Mashbo\CoreRepository\Application\Filtering\Stringifiers\UniqueKey;
use Mashbo\CoreRepository\Domain\Filtering\Filter;

class FilterStringifierRegistry
{
    /**
     * @var array<class-string<Filter>, Stringifier>
     */
    private array $stringifiers;

    /** @var array<string, Stringifier> */
    private array $stringifiersByKey;

    /**
     * @param iterable<array-key, Stringifier> $stringifiers
     */
    public function __construct(iterable $stringifiers)
    {
        $this->stringifiers = [];
        $this->stringifiersByKey = [];

        foreach ($stringifiers as $stringifier) {
            $this->stringifiers[$stringifier->getSupportedClass()] = $stringifier;

            $key = FilterStringifierRegistry::getUniqueKey($stringifier);

            if (isset($this->stringifiersByKey[$key])) {
                throw new FilterStringifierKeyAlreadyInUseException(
                    existingStringifier: $this->stringifiersByKey[$key],
                    newStringifier: $stringifier,
                    key: $key
                );
            }
            $this->stringifiersByKey[$key] = $stringifier;
        }
    }

    public function find(Filter $filter): Stringifier
    {
        $filterClass = $filter::class;
        if (array_key_exists($filterClass, $this->stringifiers)) {
            return $this->stringifiers[$filterClass]->with($filter);
        }

        throw new FilterStringifierNotFoundException($filter);
    }

    public function findByKey(string $key): Stringifier
    {
        if (array_key_exists($key, $this->stringifiersByKey)) {
            return $this->stringifiersByKey[$key];
        }

        throw new FilterStringifierNotFoundByKeyException($key);
    }

    public static function getUniqueKey(Stringifier $stringifier): string
    {
        $refl = new \ReflectionClass($stringifier);
        $uniqueKeyAttrs = $refl->getAttributes(UniqueKey::class);

        $uniqueKey = $stringifier->getKey();

        if (count($uniqueKeyAttrs) > 0) {
            $uniqueKeyAttr = reset($uniqueKeyAttrs);

            $uniqueKey = $uniqueKeyAttr->newInstance()->key;
        }

        return $uniqueKey;
    }
}
