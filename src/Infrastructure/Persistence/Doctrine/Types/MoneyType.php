<?php

namespace Mashbo\CoreRepository\Infrastructure\Persistence\Doctrine\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Money\Money;

class MoneyType extends Type
{
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if (!$value instanceof Money) {
            return null;
        }

        return $value->getAmount();
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?Money
    {
        if (!is_string($value) || !is_numeric($value)) {
            return null;
        }

        return Money::GBP($value);
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getVarcharTypeDeclarationSQL($column);
    }

    public function getName(): string
    {
        return 'money_type';
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
