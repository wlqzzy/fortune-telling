<?php

namespace FortuneTelling\model\userAttr;

use FortuneTelling\facade\core\bz\BaZi;
use FortuneTelling\helper\Lunar;

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
        if (!empty($this->data['gregorianBirthday'])) {
            return $this->data['gregorianBirthday'];
        }
        if (!empty($this->data['lunarBirthday'])) {
            $this->data['gregorianBirthday'] = BaZi::lunarTransformGregorian($this->data['lunarBirthday']);
            return $this->data['gregorianBirthday'];
        }
        return '';
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
        if (!empty($this->data['lunarBirthday'])) {
            return $this->data['lunarBirthday'];
        }
        if (!empty($this->data['gregorianBirthday'])) {
            $this->initLunar();
            return $this->data['lunarBirthday'];
        }
        return '';
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
        if (!empty($this->data['lunarBirthdayStr'])) {
            return $this->data['lunarBirthdayStr'];
        }
        if (!empty($this->data['gregorianBirthday'])) {
            $this->initLunar();
            return $this->data['lunarBirthdayStr'];
        }
        return '';
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
        if (!empty($this->data['isLeapMonth'])) {
            return $this->data['isLeapMonth'];
        }
        if (!empty($this->data['gregorianBirthday'])) {
            $this->initLunar();
            return $this->data['isLeapMonth'];
        }
        return false;
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
        if (!empty($this->data['adjacentJieQi'])) {
            return $this->data['adjacentJieQi'];
        }
        if (!empty($this->data['gregorianBirthday'])) {
            return Lunar::getAdjacentJieQi($this->data['gregorianBirthday']);
        }
        return [];
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
        if (empty($this->data['gregorianBirthday']) && empty($this->data['adjacentJieQi'])) {
            return [];
        }
        return BaZi::birthBz($this->data['gregorianBirthday'], $this->data['adjacentJieQi'], $this->is23h);
    }
}