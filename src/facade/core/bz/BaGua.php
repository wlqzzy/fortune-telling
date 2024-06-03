<?php

namespace FortuneTelling\facade\core\bz;

/**
 * @method static \FortuneTelling\core\bz\BaGua setNums() 获取基础命盘
 * @method static array ben() 获取基础命盘
 */
class BaGua
{
    /**
     * @var \FortuneTelling\core\bz\BaGua
     */
    protected static $zw;
    public static function __callStatic($method, $args)
    {
        return call_user_func_array([self::getZw(), $method], $args);
    }

    public static function getBg()
    {
        if (!self::$zw) {
            self::$zw = new \FortuneTelling\core\bz\BaGua();
        }
        return self::$zw;
    }
}