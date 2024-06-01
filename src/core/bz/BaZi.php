<?php

namespace FortuneTelling\core\bz;

use FortuneTelling\data\BaZiDb;
use FortuneTelling\helper\Lunar;

/**
 * 八字五行计算
 */
class BaZi
{

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
            strtotime($aa[0] . '-' . $aa[1] . '-' . $aa[2] . ' ' . ($dateArr[1] ?? '00:00:01'))
        );
    }


    /**
     * 公历转农历出生日期
     * @param string $date 真实出生时间
     * @return array
     * */
    public function gregorianTransformLunar(string $date): array
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
            //闰月及之后的月份数字-1
            $tdMonthNo = ($lunarDate[4] + 0) > $lunarDate[7] ? ($lunarDate[4] - 1) : $lunarDate[4];
        } else {
            $tdMonthNo = $lunarDate[4];
        }
        $tdDay = $lunarDate[2];
        $tdDayNo = $lunarDate[5];
        //出生时辰转化时支
        $time = explode(' ', $date);
        $h = explode(':', $time[1] ?? '00:00:01');
        return [
            //农历生日字符串
            'lunarBirthdayStr' => $lunarDate[3] . '年 '
                                  . $lunarMonth . ' '
                                  . $tdDay . ' '
                                  . BaZiDb::DZ_ARR[ceil($h[0] / 2)] . '时',
            //农历日期Y-m-d格式
            'lunarBirthdayNo' => $lunarDate[0]
                                 . '-' . ($tdMonthNo > 9 ? '' : '0') . $tdMonthNo
                                 . '-' . ($tdDayNo > 9 ? '' : '0') . $tdDayNo
                                 . ' ' . $h[1],
            //是否闰月
            'isLeapMonth' => ($lunarDate[7] && ($lunarDate[7] + 1 == $lunarDate[4])) ? 1 : 0
        ];
    }

    /**
     * 计算八字
     *
     * @param string $realDate
     * @param array $jieQi
     * @param int $is23
     * @return array
     *
     * @author wlq
     * @since 1.0 2023-09-15
     */
    public function birthBz(string $realDate, array $jieQi, int $is23 = 1): array
    {
        $realDateTime = strtotime($realDate);
        //取出前节气数据的年
        $year = $jieQi['prev']['year'];
        //年天干
        $yearTgNum = ($year - 4) % 10;
        //年地支
        $yearDzNum = ($year - 4) % 12;
        /**计算月干支**/
        //月地支
        $monthDzNum = $jieQi['prev']['dzNum'];
        //月干：月干下标=（(月支下标+10)%12+（年干下标+1）*2）%10
        $monthTgNum = (($jieQi['prev']['dzNum'] + 10) % 12 + ($yearTgNum + 1) * 2) % 10;
        /*计算日天干地支*/
        //日天干求解参数
        //获取年
        $year = date('Y', $realDateTime);
        //年前两位数字
        $year12 = floor($year / 100);
        //年后两位数字
        $year34 = $year % 100;
        $realMonth = date('n', $realDateTime);
        //1月、2月特殊处理
        if ($realMonth == 1 || $realMonth == 2) {
            $realMonth = $realMonth + 12;
            $year34 -= 1;
        }
        $realDay = date('j', $realDateTime);
        //23时之后为第二天时参数+1
        $hour23h = $is23 && date('G', $realDateTime) == 23 ? 1 : 0;
        //日天干求解参数
        $g = 4 * $year12
             + floor($year12 / 4)
             + 5 * $year34
             + floor($year34 / 4)
             + floor(3 * ($realMonth + 1) / 5)
             + $realDay
             - 3
             + $hour23h;
        //日地支求解参数
        $z = 8 * $year12
             + floor($year12 / 4)
             + 5 * $year34
             + floor($year34 / 4)
             + floor(3 * ($realMonth + 1) / 5)
             + $realDay
             + 7
             + ($realMonth % 2 ? 0 : 6)
             + $hour23h;
        /**获取日干支**/
        //日干
        $dayTgNum = ($g - 1) % 10;
        //日支
        $dayDzNum = ($z - 1) % 12;
        /**获取时干支**/
        //获取时干支查询列表
        $hour = date('G', $realDateTime);
        //时地支
        $hourDzNum = floor(($hour + 1) % 24 / 2);
        //时天干
        $hourTgNum = ($hourDzNum + $dayTgNum * 2) % 10;
        //获取出生时干支数据
        $hourName = ['夜半', '鸡鸣', '平旦', '日出', '食时', '隅中', '日中', '日跌', '晡食', '日入', '黄昏', '人定'];
        $data = [
            'year' => ['tg' => $yearTgNum, 'dz' => $yearDzNum,],
            'month' => ['tg' => $monthTgNum, 'dz' => $monthDzNum,],
            'day' => ['tg' => $dayTgNum, 'dz' => $dayDzNum,],
            'hour' => ['tg' => $hourTgNum, 'dz' => $hourDzNum,]
        ];
        //干支五行
        foreach ($data as &$val) {
            $val['tgText'] = BaZiDb::TG_ARR[$val['tg']];
            $val['dzText'] = BaZiDb::DZ_ARR[$val['dz']];
            $val['tgWx'] = BaZiDb::TG_WX_NUM_ARR[$val['tg']];
            $val['tgWxText'] = BaZiDb::WX_ARR[$val['tgWx']];
            $val['dzWx'] = BaZiDb::TG_WX_NUM_ARR[$val['dz']];
            $val['dzWxText'] = BaZiDb::WX_ARR[$val['dzWx']];
            $val['nyWx'] = BaZiDb::GZ_NY_WX[(($val['tg'] + 10 - $val['dz'] % 10) % 10 / 2) * 12 + $val['tg']];
        }
        $data['hourVulgo'] = $hourName[$hourDzNum];
        return $data;
    }
}
