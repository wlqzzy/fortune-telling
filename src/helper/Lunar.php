<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------
namespace FortuneTelling\helper;

use FortuneTelling\data\LunarDb;

/**
 * 文档基础模型
 */
class Lunar
{
    private const MIN_YEAR = 1891;
    private const MAX_YEAR = 2100;

    /**
     * 将阳历转换为阴历
     *
     * @param int $year 公历-年
     * @param int $month 公历-月
     * @param int $date 公历-日
     * @return array
     *
     * @author wlq
     * @since 1.0 2022-03-30
     */
    public static function convertSolarToLunar(int $year, int $month, int $date): array
    {
        $yearData = LunarDb::LUNAR_INFO[$year - self::MIN_YEAR];
        if ($year == self::MIN_YEAR && $month <= 2 && $date <= 9) {
            return LunarDb::DEFAULT_DATE;
        }
        return self::getLunarByBetween(
            $year,
            self::getDaysBetweenSolar(
                $year,
                $month,
                $date,
                $yearData[1],
                $yearData[2]
            )
        );
    }

    /**
     * 根据距离正月初一的天数计算阴历日期
     * @param int $year 阳历年
     * @param int $between 天数
     */
    public static function getLunarByBetween(int $year, int $between): array
    {
        $lunarArray = [];
        $t = 0;
        $e = 0;
        $leapMonth = 0;
        if ($between == 0) {
            $m = '正月';
            $d = '初一';
            $t = 1;
            $e = 1;
        } else {
            $year = $between > 0 ? $year : ($year - 1);
            $yearMonth = self::getLunarYearMonths($year);
            $leapMonth = self::getLeapMonth($year);
            $between = $between > 0 ? $between : (self::getLunarYearDays($year) + $between);
            for ($i = 0; $i < 13; $i++) {
                if ($between == $yearMonth[$i]) {
                    $t = $i + 2;
                    $e = 1;
                    break;
                } elseif ($between < $yearMonth[$i]) {
                    $t = $i + 1;
                    $e = $between - (empty($yearMonth[$i - 1]) ? 0 : $yearMonth[$i - 1]) + 1;
                    break;
                }
            }
            $m = ($leapMonth != 0 && $t == $leapMonth + 1)
                ? (
                    '闰' . self::getCapitalNum(
                        $t - 1,
                        true
                    )
                )
                : self::getCapitalNum(
                    ($leapMonth != 0 && $leapMonth + 1 < $t ? ($t - 1) : $t),
                    true
                );
            $d = self::getCapitalNum($e, false);
            array_push($lunarArray, $year, $m, self::getCapitalNum($e, false));
        }
        array_push(
            $lunarArray,
            $year,
            $m, //阴历月叫法
            $d, //阴历日叫法
            self::getLunarYearName($year), //阴历干支纪年
            $t, //阴历月
            $e, //阴历日
            self::getYearZodiac($year),  //生肖
            $leapMonth  // 闰几月
        );
        return $lunarArray;
    }

    /**
     * 获取农历年份的月数
     *
     * @param int $year
     * @return array
     *
     * @author wlq
     * @since 1.0 2022-03-30
     */
    public static function getLunarYearMonths(int $year): array
    {
        $monthData = self::getLunarMonths($year);
        $res = [];
        $yearData = LunarDb::LUNAR_INFO[$year - self::MIN_YEAR];
        $len = ($yearData[0] == 0 ? 12 : 13);
        for ($i = 0; $i < $len; $i++) {
            $temp = 0;
            for ($j = 0; $j <= $i; $j++) {
                $temp += $monthData[$j];
            }
            array_push($res, $temp);
        }
        return $res;
    }

    /**
     * 获取阴历每月的天数的数组
     *
     * @param int $year
     * @return array
     *
     * @author wlq
     * @since 1.0 2022-03-30
     */
    public static function getLunarMonths(int $year): array
    {
        $yearData = LunarDb::LUNAR_INFO[$year - self::MIN_YEAR];
        $leapMonth = $yearData[0];
        $bit = decbin($yearData[3]);
        $bitArray = [];
        for ($i = 0; $i < strlen($bit); $i++) {
            $bitArray[$i] = substr($bit, $i, 1);
        }
        for ($k = 0, $klen = 16 - count($bitArray); $k < $klen; $k++) {
            array_unshift($bitArray, '0');
        }
        $bitArray = array_slice($bitArray, 0, ($leapMonth == 0 ? 12 : 13));
        for ($i = 0; $i < count($bitArray); $i++) {
            $bitArray[$i] = $bitArray[$i] + 29;
        }
        return $bitArray;
    }

    /**
     * 获取闰月
     *
     * @param int $year 阴历年份
     * @return int
     *
     * @author wlq
     * @since 1.0 2022-03-30
     */
    public static function getLeapMonth(int $year): int
    {
        $yearData = LunarDb::LUNAR_INFO[$year - self::MIN_YEAR];
        return $yearData[0];
    }

    /**
     * 获取农历每年的天数
     *
     * @param int $year 农历年份
     * @return mixed
     *
     * @author wlq
     * @since 1.0 2022-03-30
     */
    public static function getLunarYearDays(int $year): int
    {
        $months = self::getLunarYearMonths($year);
        $len = count($months);
        return ($months[$len - 1] == 0 ? $months[$len - 2] : $months[$len - 1]);
    }

    /**
     * 获取数字的阴历叫法
     *
     * @param int $num 数字
     * @param bool $isMonth 是否是月份的数字
     * @return string
     *
     * @author wlq
     * @since 1.0 2022-03-30
     */
    public static function getCapitalNum(int $num, bool $isMonth): string
    {
        if (!$num || $num > 30) {
            return '';
        }
        if ($isMonth) {
            $res = LunarDb::MONTH_HASH[$num];
        } else {
            $res = $num == 20 ? '二十' : (LunarDb::TEN_DATE_HASH[floor($num / 10)] . LunarDb::DATE_HASH[$num % 10]);
        }
        return $res;
    }

    /**
     * 获取干支纪年
     *
     * @param int $year
     * @return string
     *
     * @author wlq
     * @since 1.0 2022-03-30
     */
    public static function getLunarYearName(int $year): string
    {
        $year = $year . '';
        return LunarDb::SKY[$year{3}] . LunarDb::EARTH[$year % 12];
    }

    /**
     * 根据阴历年获取生肖
     *
     * @param int $year 阴历年
     * @return string
     *
     * @author wlq
     * @since 1.0 2022-03-30
     */
    public static function getYearZodiac(int $year): string
    {
        return LunarDb::ZODIAC[$year % 12];
    }

    /**
     * 计算2个阳历日期之间的天数
     *
     * @param int $year 阳历年
     * @param int $cMonth
     * @param int $cDate
     * @param int $dMonth 阴历正月对应的阳历月份
     * @param int $dDate 阴历初一对应的阳历天数
     * @return int
     *
     * @author wlq
     * @since 1.0 2022-03-30
     */
    public static function getDaysBetweenSolar(int $year, int $cMonth, int $cDate, int $dMonth, int $dDate): int
    {
        $a = mktime(0, 0, 0, $cMonth, $cDate, $year);
        $b = mktime(0, 0, 0, $dMonth, $dDate, $year);
        return ceil(($a - $b) / 24 / 3600);
    }

    /**
     * @param int $year
     * @param int $month
     * @param int $date
     * @return array
     *
     * @author wlq
     * @since 1.0 2022-03-30
     */
    public static function convertSolarMonthToLunar(int $year, int $month, int $date): array
    {
        $yearData = LunarDb::LUNAR_INFO[$year - self::MIN_YEAR];
        if ($year == self::MIN_YEAR && $month <= 2 && $date <= 9) {
            return LunarDb::DEFAULT_DATE;
        }
        $dd = LunarDb::MONTH_DAYS[$month];
        if (self::isLeapYear($year) && $month == 2) {
            $dd++;
        }
        $lunar_ary = [];
        for ($i = 1; $i < $dd; $i++) {
            $array = self::getLunarByBetween(
                $year,
                self::getDaysBetweenSolar(
                    $year,
                    $month,
                    $i,
                    $yearData[1],
                    $yearData[2]
                )
            );
            $array[] = $year . '-' . $month . '-' . $i;
            $lunar_ary[$i] = $array;
        }
        return $lunar_ary;
    }

    /**
     * 判断是否是闰年
     *
     * @param int $year
     * @return bool
     *
     * @author wlq
     * @since 1.0 2022-03-30
     */
    public static function isLeapYear(int $year): bool
    {
        return (($year % 4 == 0 && $year % 100 != 0) || ($year % 400 == 0));
    }

    /**
     * 将阴历转换为阳历
     *
     * @param int $year 阴历-年
     * @param int $month 阴历-月，闰月处理：例如如果当年闰五月，那么第二个五月就传六月，相当于阴历有13个月，只是有的时候第13个月的天数为0
     * @param int $date 阴历-日
     * @return array
     *
     * @author wlq
     * @since 1.0 2022-03-30
     */
    public static function convertLunarToSolar(int $year, int $month, int $date): array
    {
        $yearData = LunarDb::LUNAR_INFO[$year - self::MIN_YEAR];
        $between = self::getDaysBetweenLunar($year, $month, $date);
        $res = mktime(0, 0, 0, $yearData[1], $yearData[2], $year);
        $res = date('Y-m-d', $res + $between * 24 * 60 * 60);
        $day = explode('-', $res);
        $year = $day[0];
        $month = $day[1];
        $day = $day[2];
        return [$year, $month, $day];
    }

    /**
     * 计算阴历日期与正月初一相隔的天数
     *
     * @param int $year
     * @param int $month
     * @param int $date
     * @return int
     *
     * @author wlq
     * @since 1.0 2022-03-30
     */
    public static function getDaysBetweenLunar(int $year, int $month, int $date): int
    {
        $yearMonth = self::getLunarMonths($year);
        $res = 0;
        for ($i = 1; $i < $month; $i++) {
            $res += $yearMonth[$i - 1];
        }
        $res += $date - 1;
        return $res;
    }

    /**
     * 获取阳历月份的天数
     *
     * @param int year 阳历-年
     * @param int month 阳历-月
     * @return int
     *
     * @author wlq
     * @since 1.0 2022-03-30
     */
    public static function getSolarMonthDays(int $year, int $month): int
    {
        $days = LunarDb::MONTH_DAYS[$month - 1];
        if ($month == 2 && self::isLeapYear($year)) {
            $days++;
        }
        return $days;
    }

    /**
     * 获取阴历月份的天数
     *
     * @param int $year 阴历-年
     * @param int $month 阴历-月，从一月开始
     * @return int
     *
     * @author wlq
     * @since 1.0 2022-03-30
     */
    public static function getLunarMonthDays($year, $month): int
    {
        $monthData = self::getLunarMonths($year);
        return $monthData[$month - 1];
    }

    /**
     * 获取前一个节气信息
     *
     * @param string $date
     * @return array
     *
     * @author wlq
     * @since 1.0 2022-03-30
     */
    public static function getPrevJieQi(string $date): array
    {
        $dateInt = strtotime($date);
        $year = date('Y', $dateInt);
        $jieQiIndex = date('n', $dateInt) - 1;
        while (true) {
            $jieQiDetail = self::getJieQiDay($year, $jieQiIndex);
            /**
             * 当前日期大于等于节气日期，则该节气为前一个节气
             */
            if ($dateInt >= $jieQiDetail['dateInt']) {
                $jieQiDetail['index'] = $jieQiIndex;
                return $jieQiDetail;
            }
            $jieQiIndex = $jieQiIndex - 1;
            //当前判断节点节气不为前一个节气且该节气为第一个节气时，下次获取节气的年份减1
            if ($jieQiIndex < 0) {
                $year -= 1;
            }
        }
    }

    /**
     * 获取后一个节气
     *
     * @param string $date
     * @return array
     *
     * @author wlq
     * @since 1.0 2023-09-12
     */
    public static function getNextJieQi(string $date): array
    {
        $dateInt = strtotime($date);
        $year = date('Y', $dateInt);
        $jieQiIndex = date('n', $dateInt) - 1;
        while (true) {
            $jieQiDetail = self::getJieQiDay($year, $jieQiIndex);
            /**
             * 当前日期小于节气日期，则该节气为前一个节气
             */
            if ($dateInt < $jieQiDetail['dateInt']) {
                $jieQiDetail['index'] = $jieQiIndex;
                return $jieQiDetail;
            }
            $jieQiIndex = $jieQiIndex + 1;
            //当前判断节点节气不为前一个节气且该节气为第一个节气时，下次获取节气的年份减1
            if ($jieQiIndex > 11) {
                $year += 1;
            }
        }
    }

    /**
     * 获取前后相邻的节气
     *
     * @param string $date
     * @return array
     *
     * @author wlq
     * @since 1.0 2023-09-12
     */
    public static function getAdjacentJieQi(string $date): array
    {
        $prevJieQi = self::getPrevJieQi($date);
        $year = $prevJieQi['year'] + ($prevJieQi['index'] == 11 ? 1 : 0);
        $nextJieQi = self::getJieQiDay($year, ($prevJieQi['index'] + 1) % 12);
        return [
            'prev' => $prevJieQi,
            'next' => $nextJieQi,
        ];
    }

    /**
     * 获取指定年指定节点的节气信息
     *
     * @param $year
     * @param int $index
     * @return array
     *
     * @author wlq
     * @since 1.0 2023-09-12
     */
    public static function getJieQiDay(int $year, int $index): array
    {
        $index = ($index + 12) % 12;
        $jieQi = LunarDb::JIE_QI_DETAIL[$index];
        $diffYear = $jieQi['month'] < 3 ? -1 : 0;
        $y = $year % 100;
        $c = $jieQi['c'][ceil($year / 100)];
        $day = ($y * 2.2422 + $c) - ceil(($y + $diffYear) / 4) + $jieQi['diff'][$year] ?? 0;
        $jieQi['year'] = $year;
        $jieQi['day'] = $day;
        $jieQi['dateInt'] = strtotime($year . '-' . $jieQi['month'] . '-' . $day);
        $jieQi['dateStr'] = date('Y-m-d', $jieQi['dateInt']);
        return $jieQi;
    }
}
