<?php

namespace FortuneTelling\helper;


use FortuneTelling\facade\core\bz\BaZi;

trait BaZiAttr
{
    protected $data;
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
            return BaZi::lunarTransformGregorian($this->data['lunarBirthday']);
        }
        return '';
    }

    private function getLunarBirthday(): string
    {

    }
}