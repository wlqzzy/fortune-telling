<?php

namespace FortuneTelling\facade;

/**
 * @method int getLeapMonth(int $year) static 获取闰月
 * @method array convertLunarToSolar(int $year, int $month, int $date) static 将阴历转换为阳历
 * @method array convertSolarToLunar(int $year, int $month, int $date) static 将阳历转换为阴历
 */
class Lunar
{
    protected static $lunar;
    public static function __callStatic($method, $args)
    {
        if (!self::$lunar) {
            self::$lunar = new \FortuneTelling\core\Lunar();
        }
        return call_user_func_array([self::$lunar, $method], $args);
    }
}