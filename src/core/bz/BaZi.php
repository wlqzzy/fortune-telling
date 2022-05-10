<?php

namespace FortuneTelling\core\bz;

use FortuneTelling\data\BaZiDb;
use FortuneTelling\facade\Lunar;

/**
 * 八字五行计算
 */
class BaZi
{
    protected $use_new = false;
    protected $is23H = true;

    /**
     * 农历转公历
     * @param string $date 出生日期
     * @param bool $isLeapMonth 是否闰月
     * @return string
     *
     * @author wlq
     * @since 1.0 2022-03-30
     */
    public function lunarTransformGregorian(string $date, bool $isLeapMonth = false): string
    {
        //拆分年月日
        $dateArr = explode(' ', $date);
        $dateYMD = explode('-', $dateArr[0]);
        $year = $dateYMD[0] + 0;
        $month = $dateYMD[1] + 0;
        $day = $dateYMD[2] + 0;
        //调取时间插件模型
        //获取闰月重新赋值月
        $rm = Lunar::getLeapMonth((int)$year);
        if ($month == $rm + 0 && $isLeapMonth) {
            $month = $rm + 1;
        } elseif ($month > $rm && $rm) {
            $month = $month + 1;
        }
        //调取插件获取公历日期信息
        $aa = Lunar::convertLunarToSolar($year, $month, $day); //农历转公历
        return date(
            'Y-m-d H:i:01',
            strtotime($aa[0] . '-' . $aa[1] . '-' . $aa[2] . ' ' . $dateArr[1])
        );
    }


    /**
     * 农历出生日期
     * @param string $date 真实出生时间
     * @return array
     * */
    public function gregorianTransformLunar(string $date)
    {
        //取出出生时间的年、月、日
        $dateArr = explode(' ', $date);
        $dateYMD = explode('-', $dateArr[0]);
        $year = $dateYMD[0];
        $month = $dateYMD[1];
        $day = $dateYMD[2];
        //调用转换插件获取农历信息
        //调取时间插件模型
        //调取公历转农历函数
        $lunarDate = Lunar::convertSolarToLunar($year, $month, $day);
        //处理农历信息
        $lunarMonth = $lunarDate[1];
        //闰月判断处理
        if ($lunarDate[7]) {
            $lunar['is_ry'] = ($lunarDate[7] == $lunarMonth) ? 1 : 0;//判断闰月
            //闰月及之后的月份数字-1
            $td_month_no = ($lunarDate[4] + 0) > $lunarDate[7] ? ($lunarDate[4] - 1) : $lunarDate[4];
        } else {
            $td_month_no = $lunarDate[4];
        }
        $td_day = $lunarDate[2];
        $td_day_no = $lunarDate[5];
        //出生时辰转化时支
        $time = explode(' ', $date);
        $h = explode(':', $time[1]);
        return [
            'lunarBirthdayStr' => $lunarDate[3] . '年 ' . $lunarMonth . ' ' . $td_day . ' ' . BaZiDb::$earth[ceil($h[0] / 2)] . '时',
            'lunarBirthdayNo' => $lunarDate[0] . '-' . ($td_month_no > 9 ? '' : '0') . $td_month_no . '-' . ($td_day_no > 9 ? '' : '0') . $td_day_no . ' ' . $h[1],
            'isLeapMonth' => mb_strstr($lunarMonth, '闰', 'utf-8') !== false
        ];
    }
    private function
}
