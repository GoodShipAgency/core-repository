<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Application\Filtering;

use Mashbo\CoreRepository\Application\Filtering\Exception\FilterStringifierKeyAlreadyInUseException;
use Mashbo\CoreRepository\Application\Filtering\Exception\FilterStringifierNotFoundByKeyException;
use Mashbo\CoreRepository\Application\Filtering\Exception\FilterStringifierNotFoundException;
use Mashbo\CoreRepository\Application\Filtering\Stringifiers\Stringifier;
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

            if (isset($this->stringifiersByKey[$stringifier->getKey()])) {
                throw new FilterStringifierKeyAlreadyInUseException(
                    existingStringifier: $this->stringifiersByKey[$stringifier->getKey()],
                    newStringifier: $stringifier,
                    key: $stringifier->getKey()
                );
            }
            $this->stringifiersByKey[$stringifier->getKey()] = $stringifier;
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
}
