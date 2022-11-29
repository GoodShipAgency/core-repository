<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Ports;

class UrlRegexPattern
{
    public const Uuid = '([a-f0-9-]+)';
    public const Email = '(.+)\@(.+)';
    public const String = '([A-Za-z0-9]+)';
}
