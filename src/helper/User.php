<?php

namespace FortuneTelling\helper;

use FortuneTelling\model\UserModel;

class User
{
    /**
     * @var UserModel
     */
    protected static $user = null;

    public static function init(array $data): UserModel
    {
        self::$user = new UserModel($data);
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