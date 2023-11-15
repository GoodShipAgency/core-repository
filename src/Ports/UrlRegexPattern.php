<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Ports;

use Symfony\Component\Routing\Requirement\Requirement;

class UrlRegexPattern
{
    public const Uuid = Requirement::UUID;
    public const UuidV4 = Requirement::UUID_V4;
    public const UuidV5 = Requirement::UUID_V5;
    public const UuidV6 = Requirement::UUID_V6;
    public const UuidV7 = Requirement::UUID_V7;
    public const UuidV8 = Requirement::UUID_V8;
    public const Email = Requirement::ASCII_SLUG . '@' . Requirement::ASCII_SLUG;
    public const String = Requirement::ASCII_SLUG;
    public const Date = Requirement::DATE_YMD;
    public const Digits = Requirement::DIGITS;
    public const PositiveInteger = Requirement::POSITIVE_INT;
}
