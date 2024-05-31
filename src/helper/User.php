<?php

namespace FortuneTelling\helper;

use FortuneTelling\model\UserModel;

class User
{
    /**
     * @var UserModel
     */
    protected static $user = null;

    /**
     * 初始化
     *
     * @param string $date  日期时间，格式：Y-m-d H:i:s
     * @param int $dateType 日期类型，1=阳历，2=阴历
     * @param bool $isLeapMonth 是否是闰月
     * @param int $sex  性别，0=女，1=男
     * @param string $city
     * @return UserModel
     *
     * @author wlq
     * @since 1.0 2024-05-31
     */
    public static function init(
        string $date,
        int $dateType = 1,
        bool $isLeapMonth = false,
        int $sex = 0,
        string $city = ''
    ): UserModel {
        self::$user = new UserModel([
            'gregorianBirthday' => $dateType == 1 ? $date : '',//出生日期-阳历
            'lunarBirthday' => $dateType == 2 ? $date : '',//出生日期-阴历
            'isLeapMonth' => $isLeapMonth,//是否为农历闰月，默认否
            'sex' => $sex,
            'city' => $city,
        ]);
        return self::$user;
    }

    /**
     * 获取登录信息
     *
     * @return UserModel
     *
     * @author aiChenK
     */
    public static function info(): UserModel
    {
        if (self::$user) {
            return self::$user;
        }
        return new UserModel();
    }
}