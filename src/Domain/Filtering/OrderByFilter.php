<?php

declare(strict_types=1);

namespace CoreRepository\Domain\Filtering;

abstract class OrderByFilter implements Filter
{
    public const ORDER_DESC = 'DESC';
    public const ORDER_ASC = 'ASC';

    public function __construct(private string $order)
    {
        if (!in_array($order, [self::ORDER_DESC, self::ORDER_ASC])) {
            throw new \LogicException("Order must be DESC or ASC. '{$order}' given.");
        }
    }

    public function getOrder(): string
    {
        return $this->order;
    }
}
