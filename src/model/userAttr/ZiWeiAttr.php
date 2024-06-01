<?php

namespace FortuneTelling\model\userAttr;

use FortuneTelling\facade\core\bz\BaZi;
use FortuneTelling\facade\core\bz\ZiWei;
use FortuneTelling\helper\Lunar;

/**
 * Trait BaZiAttr
 * @package FortuneTelling\model\userAttr
 * @property  string $gregorianBirthday
 * @property  string $lunarBirthday
 * @property  string $lunarBirthdayStr
 * @property  bool $isLeapMonth
 * @property  array $adjacentJieQi
 * @property  array $baZi
 * @property  \FortuneTelling\core\bz\ZiWei $mp
 */
trait ZiWeiAttr
{

    /**
     * 命盘初始化
     *
     * @author wlq
     * @since 1.0 2023-09-18
     */
    private function mpInit(): bool
    {
        if (ZiWei::checkInit()) {
            return true;
        }
        $this->data['mp'] = ZiWei::getZw();
        if (!$this->gregorianBirthday && !$this->lunarBirthday) {
            return false;
        }
        $this->initLunar();
        $time = strtotime($this->lunarBirthday . ':00:01');
        $this->data['mp']->init(
            $this->baZi['hour']['dz'],
            $this->sex,
            date('Y', $time),
            date('n', $time),
            date('j', $time),
            $this->isLeapMonth ? 1 : 0
        );
        return true;
    }

    /**
     * 获取命盘信息
     *
     * @return array
     *
     * @author wlq
     * @since 1.0 2023-09-18
     */
    private function getMp(): \FortuneTelling\core\bz\ZiWei
    {
        if (empty($this->data['mp'])) {
            $this->mpInit();
        }
        return $this->data['mp'];
    }
}