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
 * @property  array $mp
 */
trait BaZiAttr
{
    protected $data;
    protected $is23h = true;
    /**
     * 获取阳历出生日期
     *
     * @return string
     *
     * @author wlq
     * @since 1.0 2022-03-30
     */
    private function getGregorianBirthday(): string
    {
        if (empty($this->data['gregorianBirthday']) && !empty($this->data['lunarBirthday'])) {
            $this->data['gregorianBirthday'] = BaZi::lunarTransformGregorian($this->data['lunarBirthday']);
        }
        return $this->data['gregorianBirthday'];
    }

    /**
     * 获取农历出生日期-Y-m-d格式
     *
     * @return string
     *
     * @author wlq
     * @since 1.0 2023-09-12
     */
    private function getLunarBirthday(): string
    {
        if (empty($this->data['lunarBirthday']) && !empty($this->data['gregorianBirthday'])) {
            $this->initLunar();
            return $this->data['lunarBirthday'];
        }
        return $this->data['lunarBirthday'];
    }

    /**
     * 获取农历出生日期-中文格式
     *
     * @return string
     *
     * @author wlq
     * @since 1.0 2023-09-12
     */
    private function getLunarBirthdayStr(): string
    {
        if (empty($this->data['lunarBirthdayStr']) && !empty($this->data['gregorianBirthday'])) {
            $this->initLunar();
        }
        return $this->data['lunarBirthdayStr'];
    }

    /**
     * 获取是否闰月
     *
     * @return string
     *
     * @author wlq
     * @since 1.0 2023-09-12
     */
    private function getIsLeapMonth(): bool
    {
        if (empty($this->data['lunarBirthday']) && !empty($this->data['gregorianBirthday'])) {
            $this->initLunar();
        }
        return $this->data['isLeapMonth'];
    }

    /**
     * 初始化农历信息
     *
     * @author wlq
     * @since 1.0 2023-09-12
     */
    private function initLunar(): void
    {
        $data = BaZi::gregorianTransformLunar($this->data['gregorianBirthday']);
        $this->data['lunarBirthday'] = $data['lunarBirthdayNo'];
        $this->data['lunarBirthdayStr'] = $data['lunarBirthdayStr'];
        $this->data['isLeapMonth'] = $data['isLeapMonth'];
    }

    /**
     * 获取相邻节气
     *
     * @return array
     *
     * @author wlq
     * @since 1.0 2023-09-12
     */
    private function getAdjacentJieQi(): array
    {
        if (empty($this->data['adjacentJieQi']) && !empty($this->data['gregorianBirthday'])) {
            $this->data['adjacentJieQi'] = Lunar::getAdjacentJieQi($this->data['gregorianBirthday']);
            return $this->data['adjacentJieQi'];
        }
        return $this->data['adjacentJieQi'] ?? [];
    }

    /**
     * 获取八字信息
     *
     * @return array
     *
     * @author wlq
     * @since 1.0 2023-09-15
     */
    private function getBaZi(): array
    {
        if (empty($this->data['baZi'])) {
            if (empty($this->data['gregorianBirthday']) && empty($this->data['adjacentJieQi'])) {
                $this->data['baZi'] = BaZi::birthBz($this->gregorianBirthday, $this->adjacentJieQi, $this->is23h);
                return $this->data['baZi'];
            }
        }
        return $this->data['baZi'] ?? [];
;
    }

    /**
     * 获取命盘信息
     *
     * @return array
     *
     * @author wlq
     * @since 1.0 2023-09-18
     */
    private function getMp(): array
    {
        if (empty($this->data['mp'])) {
            if ($this->mpInit()) {
                $this->data['mp'] = ZiWei::getMp();
            }
        }
        return $this->data['mp'] ?? [];
    }

    /**
     * 获取命盘命宫信息
     *
     * @return array
     *
     * @author wlq
     * @since 1.0 2023-09-18
     */
    private function getMpMg(): array
    {
        if (empty($this->data['mpMg'])) {
            if ($this->mpInit()) {
                $this->data['mpMg'] = ZiWei::getMpMg();
            }
        }
        return $this->data['mpMg'] ?? [];
    }

    /**
     * 获取命盘身宫信息
     *
     * @return array
     *
     * @author wlq
     * @since 1.0 2023-09-18
     */
    private function getMpSg(): array
    {
        if (empty($this->data['mpSg'])) {
            if ($this->mpInit()) {
                $this->data['mpSg'] = ZiWei::getMpSg();
            }
        }
        return $this->data['mpSg'] ?? [];
    }

    /**
     * 获取命盘流年命宫信息
     *
     * @return array
     *
     * @author wlq
     * @since 1.0 2023-09-18
     */
    private function getMpLnMg(): array
    {
        if (empty($this->data['mpLnMg'])) {
            if ($this->mpInit()) {
                $this->data['mpLnMg'] = ZiWei::getMpLnMgm();
            }
        }
        return $this->data['mpLnMg'] ?? [];
    }

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
        if (
            !empty($this->data['gregorianBirthday'])
            && !empty($this->data['adjacentJieQi'])
            && !empty($this->data['lunarBirthday'])
        ) {
            $time = strtotime($this->data['lunarBirthday']);
            $this->data['mp'] = ZiWei::init(
                $this->data['baZi']['hour']['dz']['value'],
                $this->data['sex'],
                date('Y', $time),
                date('n', $time),
                date('j', $time),
                $this->data['isLeapMonth'] ? 1 : 0
            );
            return true;
        }
        return false;
    }
}