<?php

namespace App\Helpers;

class TranslationHelper
{
    public static function transGroup(string $key, string $group)
    {
        return $group.'.'.$key;
    }
}
