<?php

namespace Mashbo\CoreRepository\Application\Filtering\Stringifiers;

#[\Attribute(\Attribute::TARGET_CLASS)]
class UniqueKey
{
    public function __construct(public string $key)
    {
    }

}