<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Domain\Filtering;

interface UserFilter extends Filter
{
    public function getUserId(): int;
}
