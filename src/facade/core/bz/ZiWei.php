<?php

namespace FortuneTelling\facade\core\bz;

/**
 * @method static \FortuneTelling\core\bz\ZiWei init(int $hourDzNum, int $sex, int $lunarBirthYear, int $lunarBirthMonth, int $lunarBirthDay, int $isLeapMonth = 0, int $liuNianLunarYear = 0)  初始化用户信息
 * @method static \FortuneTelling\core\bz\ZiWei setLiuNianLunarYear(int $liuNianLunarYear)  更改流年年份
 * @method static bool checkInit() 校验是否已初始化
 * @method static array getMpBase() 获取基础命盘
 * @method static array getMpMg()  获取命宫信息
 * @method static array getMpSg()  获取身宫信息
 * @method static array getMpLnMgm()    获取流年命宫下标
 * @method static array getWj() 获取五局信息
 * @method static array getMp() 获取命盘信息
 */
class ZiWei
{
    protected static $bz;
    public static function __callStatic($method, $args)
    {
        if (!self::$bz) {
            self::$bz = new \FortuneTelling\core\bz\ZiWei();
        }
        return call_user_func_array([self::$bz, $method], $args);
    }
}