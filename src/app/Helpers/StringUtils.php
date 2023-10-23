<?php

namespace App\Helpers;

class StringUtils
{
    public static function camelToSnakeCase(string $string): string
    {
        return ltrim(strtolower(preg_replace('/[A-Z]([A-Z](?![a-z]))*/', '_$0', $string)), '_');
    }

    public static function snakeToCamelCase(string $string): string
    {
        return str_replace('_', '', ucwords($string, '_'));
    }
}
