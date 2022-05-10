<?php

namespace FortuneTelling\facade\core\bz;

/**
 * @method string lunarTransformGregorian(string $birthday, bool $isLeapMonth = false) static 农历转公历
 */
class BaZi
{
    protected static $bz;
    public static function __callStatic($method, $args)
    {
        if (!self::$bz) {
            self::$bz = new \FortuneTelling\core\bz\BaZi();
        }
        return call_user_func_array([self::$bz, $method], $args);
    }
}