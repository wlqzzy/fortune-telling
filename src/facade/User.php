<?php

namespace FortuneTelling\facade\data;

use FortuneTelling\core\User as UserCore;

class User
{
    /**
     * @var UserCore
     */
    protected static $user = null;

    public static function init(array $data): UserCore
    {
        self::$user = new UserCore($data);
        return self::$user;
    }

    /**
     * 获取登录信息
     *
     * @return UserCore
     *
     * @author aiChenK
     */
    public static function info(): UserCore
    {
        if (self::$user) {
            return self::$user;
        }
        return new UserCore();
    }
}