<?php

namespace Mashbo\CoreRepository\Domain\Filtering;

interface UserFilter extends Filter
{
    public function getUserId(): int;
}