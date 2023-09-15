<?php

namespace FortuneTelling\facade\core\bz;

/**
 * @method static string lunarTransformGregorian(string $birthday, bool $isLeapMonth = false)   农历转公历
 * @method static array gregorianTransformLunar(string $date)   公历转农历出生日期
 * @method static array birthBz(string $realDate, array $jieQi, int $is23 = 1) 计算八字
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