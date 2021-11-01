<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Domain\Filtering;

trait UserFilterTrait
{
    private int $userId;

    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }
}
