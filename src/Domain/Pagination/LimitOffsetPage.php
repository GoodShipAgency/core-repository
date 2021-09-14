<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Domain\Pagination;

class LimitOffsetPage
{
    private int $limit;
    private int $page;

    private function __construct(int $page, int $limit)
    {
        $this->limit = $limit;
        $this->page = $page;
    }

    public static function fromPageAndLimit(int $page, int $limit): self
    {
        return new self($page, $limit);
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function getOffset(): int
    {
        return $this->limit * ($this->page - 1);
    }

    public function next(): self
    {
        return new self($this->page + 1, $this->limit);
    }

    public function previous(): self
    {
        return new self($this->page - 1, $this->limit);
    }

    public function getPageNumber(): int
    {
        return $this->page;
    }
}
