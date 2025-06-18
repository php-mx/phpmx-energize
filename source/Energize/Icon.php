<?php

namespace Energize;

use PhpMx\Import;

abstract class Icon
{
    protected static $cache = [];

    static function get(string $iconRef, ...$styleClass): string
    {
        $svg = self::svg($iconRef);
        $class = implode(' ', $styleClass);
        return "<span class='icon $class'>$svg</span>";
    }

    static function svg(string $iconRef): string
    {
        $iconRef = str_replace('.', '/', $iconRef);

        self::$cache[$iconRef] = self::$cache[$iconRef] ?? Import::content("storage/icon/$iconRef.svg");

        return empty(self::$cache[$iconRef]) ? self::svg('_none') : self::$cache[$iconRef];
    }
}
