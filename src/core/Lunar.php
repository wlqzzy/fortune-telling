<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------
namespace FortuneTelling\core;

use FortuneTelling\data\LunarDb;

/**
 * 文档基础模型
 */
class Lunar
{
    private $MIN_YEAR = 1891;
    private $MAX_YEAR = 2100;

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
    public function convertSolarToLunar(int $year, int $month, int $date): array
    {
        $yearData = LunarDb::$lunarInfo[$year - $this->MIN_YEAR];
        if ($year == $this->MIN_YEAR && $month <= 2 && $date <= 9) {
            return LunarDb::$defaultDate;
        }
        return $this->getLunarByBetween(
            $year,
            $this->getDaysBetweenSolar(
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
    public function getLunarByBetween(int $year, int $between): array
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
            $yearMonth = $this->getLunarYearMonths($year);
            $leapMonth = $this->getLeapMonth($year);
            $between = $between > 0 ? $between : ($this->getLunarYearDays($year) + $between);
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
                    '闰' . $this->getCapitalNum(
                        $t - 1,
                        true
                    )
                )
                : $this->getCapitalNum(
                    ($leapMonth != 0 && $leapMonth + 1 < $t ? ($t - 1) : $t),
                    true
                );
            $d = $this->getCapitalNum($e, false);
            array_push($lunarArray, $year, $m, $this->getCapitalNum($e, false));
        }
        array_push(
            $lunarArray,
            $year,
            $m, //阴历月叫法
            $d, //阴历日叫法
            $this->getLunarYearName($year), //阴历干支纪年
            $t, //阴历月
            $e, //阴历日
            $this->getYearZodiac($year),  //生肖
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
    public function getLunarYearMonths(int $year): array
    {
        $monthData = $this->getLunarMonths($year);
        $res = [];
        $yearData = LunarDb::$lunarInfo[$year - $this->MIN_YEAR];
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
    public function getLunarMonths(int $year): array
    {
        $yearData = LunarDb::$lunarInfo[$year - $this->MIN_YEAR];
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
    public function getLeapMonth(int $year): int
    {
        $yearData = LunarDb::$lunarInfo[$year - $this->MIN_YEAR];
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
    public function getLunarYearDays(int $year): int
    {
        $months = $this->getLunarYearMonths($year);
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
    public function getCapitalNum(int $num, bool $isMonth): string
    {
        if (!$num || $num > 30) {
            return '';
        }
        if ($isMonth) {
            $res = LunarDb::$monthHash[$num];
        } else {
            $res = $num == 20 ? '二十' : (LunarDb::$tenDateHash[floor($num / 10)] . LunarDb::$dateHash[$num % 10]);
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
    public function getLunarYearName(int $year): string
    {
        $year = $year . '';
        return LunarDb::$sky[$year{3}] . LunarDb::$earth[$year % 12];
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
    public function getYearZodiac(int $year): string
    {
        return LunarDb::$zodiac[$year % 12];
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
    public function getDaysBetweenSolar(int $year, int $cMonth, int $cDate, int $dMonth, int $dDate): int
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
    public function convertSolarMonthToLunar(int $year, int $month, int $date): array
    {
        $yearData = LunarDb::$lunarInfo[$year - $this->MIN_YEAR];
        if ($year == $this->MIN_YEAR && $month <= 2 && $date <= 9) {
            return LunarDb::$defaultDate;
        }
        $dd = LunarDb::$monthDays[$month];
        if ($this->isLeapYear($year) && $month == 2) {
            $dd++;
        }
        $lunar_ary = [];
        for ($i = 1; $i < $dd; $i++) {
            $array = $this->getLunarByBetween(
                $year,
                $this->getDaysBetweenSolar(
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
    public function isLeapYear(int $year): bool
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
    public function convertLunarToSolar(int $year, int $month, int $date): array
    {
        $yearData = LunarDb::$lunarInfo[$year - $this->MIN_YEAR];
        $between = $this->getDaysBetweenLunar($year, $month, $date);
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
    public function getDaysBetweenLunar(int $year, int $month, int $date): int
    {
        $yearMonth = $this->getLunarMonths($year);
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
    public function getSolarMonthDays(int $year, int $month): int
    {
        $days = LunarDb::$monthDays[$month - 1];
        if ($month == 2 && $this->isLeapYear($year)) {
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
    public function getLunarMonthDays($year, $month): int
    {
        $monthData = $this->getLunarMonths($year);
        return $monthData[$month - 1];
    }

//    /**
//     * 获得一年节气明细
//     *
//     * @param string $daytime
//     * @return array
//     *
//     * @author wlq
//     * @since 1.0 2022-03-30
//     */
//    public function getJieqiDetail(string $daytime)
//    {
//        $detailJieQi = [
//            '1' => [
//                "name" => "立春",
//                "ename" => "the Beginning of Spring",
//                "time" => "2月3~5日",
//                "sanhou" => "东风解冻、蛰虫始振、鱼上冰。",
//                "content" => "斗指东北。太阳黄经为315度。是二十四个节气的头一个节气。其含意是开始进入春天，“阳和起蛰，品物皆春”，过了立春，万物复苏生机勃勃，一年四季从此开始了。"],
//            '2' => [
//                "name" => "雨水",
//                "ename" => "Rain Water",
//                "time" => "2月18~20日",
//                "sanhou" => "獭祭鱼、鸿雁来、草木萌动。",
//                "content" => "斗指壬。太阳黄经为330°。这时春风遍吹，冰雪融化，空气湿润，雨水增多，所以叫雨水。人们常说：“立春天渐暖，雨水送肥忙”。"],
//            '3' => [
//                "name" => "惊蛰",
//                "ename" => "the Waking of Insects",
//                "time" => "3月5~7日",
//                "sanhou" => "桃始花、仓庚鸣、鹰化为鸠。",
//                "content" => "斗指丁。太阳黄经为345°。这个节气表示“立春”以后天气转暖，春雷开始震响，蛰伏在泥土里的各种冬眠动物将苏醒过来开始活动起来，所以叫惊蛰。这个时期过冬的虫排卵也要开始孵化。我国部分地区过入了春耕季节。谚语云：“惊蛰过，暖和和，蛤蟆老角唱山歌。”“惊蛰一犁土，春分地气通。”“惊蛰没到雷先鸣，大雨似蛟龙。"],
//            '4' => [
//                "name" => "春分",
//                "ename" => "the Spring Equinox",
//                "time" => "3月20~21日",
//                "sanhou" => "玄鸟至、雷乃发声、始电。",
//                "content" => "斗指壬。太阳黄经为0°。春分日太阳在赤道上方。这是春季90天的中分点，这一天南北两半球昼夜相等，所以叫春分。这天以后太阳直射位置便向北移，北半球昼长夜短。所以春分是北半球春季开始。我国大部分地区越冬作物进入春季生长阶段。各地农谚有：“春分在前，斗米斗钱”（广东）、“春分甲子雨绵绵，夏分甲子火烧天”（四川）、“春分有雨家家忙，先种瓜豆后插秧”（湖北）、“春分种菜，大暑摘瓜”（湖南）、“春分种麻种豆，秋分种麦种蒜”（安徽）。 "],
//            '5' => [
//                "name" => "清明",
//                "ename" => "Pure Brightness",
//                "time" => "4月4~6日",
//                "sanhou" => "桐始华、鼠化为鴽、虹始见。",
//                "content" => "斗指丁。太阳黄经为15°。此时气候清爽温暖，草木始发新枝芽，万物开始生长，农民忙于春耕春种。从前，在清明节这一天，有些人家都在门口插上杨柳条，还到郊外踏青，祭扫坟墓，这是古老的习俗。"],
//            '6' => [
//                "name" => "谷雨",
//                "ename" => "Grain Rain",
//                "time" => "4月19~21日",
//                "sanhou" => "萍始生、鸣鸠拂其羽、戴胜降于桑。",
//                "content" => "斗指癸。太阳黄经为30°。就是雨水生五谷的意思，由于雨水滋润大地五谷得以生长，所以，谷雨就是“雨生百谷”。谚云“谷雨前后，种瓜种豆”。 "],
//            '7' => [
//                "name" => "立夏",
//                "ename" => "the Beginning of Summer",
//                "time" => "5月5~7日",
//                "sanhou" => "蝼蝈鸣、蚯蚓出、王瓜生。",
//                "content" => "斗指东南。太阳黄经为45°。是夏季的开始，从此进入夏天，万物旺盛大。习惯上把立夏当作是气温显著升高，炎暑将临，雷雨增多，农作物进入旺季生长的一个最重要节气。"],
//            '8' => [
//                "name" => "小满",
//                "ename" => "Lesser Fullness of Grain",
//                "time" => "5月20~22日",
//                "sanhou" => "苦菜秀、靡草死、小暑至。",
//                "content" => "斗指甲。太阳黄经为60°。从小满开始，大麦、冬小麦等夏收作物，已经结果、籽粒饱满，但尚未成熟，所以叫小满。"],
//            '9' => [
//                "name" => "芒种",
//                "ename" => "Grain in Beard",
//                "time" => "6月5~7日",
//                "sanhou" => "螳螂生、鵙始鸣、反舌无声。",
//                "content" => "北斗指向已。太阳黄经为75°。这时最适合播种有芒的谷类作物，如晚谷、黍、稷等。如过了这个时候再种有芒和作物就不好成熟了。同时，“芒”指有芒作物如小麦、大麦等，“种”指种子。芒种即表明小麦等有芒作物成熟。芒种前后，我国中部的长江中、下游地区，雨量增多，气温升高，进入连绵阴雨的梅雨季节，空气非常潮湿，天气异常闷热，各种器具和衣物容易发霉，所以在我国长江中、下游地区也叫“霉雨”。"],
//            '10' => [
//                "name" => "夏至",
//                "ename" => "the Summer Solstice",
//                "time" => "6月21~22日",
//                "sanhou" => "鹿角解、蜩始鸣、半夏生。",
//                "content" => "北斗指向乙。太阳黄经为90°。太阳在黄经90°“夏至点”时，阳光几乎直射北回归线上空，北半球正午太阳最高。这一天是北半球白昼最长、黑夜最短的一天，从这一天起，进入炎热季节，天地万物在此时生长最旺盛。所心以古时候又把这一天叫做日北至，意思是太阳运生到最北的一日。过了夏至，太阳逐渐向南移动，北半球白昼一天比一天缩短，黑夜一天比一天加长。"],
//            '11' => [
//                "name" => "小暑",
//                "ename" => "Lesser Heat",
//                "time" => "7月6~8日",
//                "sanhou" => "温风至、蟋蟀居辟、鹰乃学习。",
//                "content" => "斗指辛。太阳黄经为105°。天气已经很热，但不到是热的时候，所以叫小暑。此时，已是初伏前后。"],
//            '12' => [
//                "name" => "大暑",
//                "ename" => "Greater Heat ",
//                "time" => "7月22~24日",
//                "sanhou" => "腐草化为萤、土润溽暑、大雨时行。",
//                "content" => "斗指丙。太阳黄经为120°。大暑是一年中最热的节气，正值勤二伏前后，长江流域的许多地方，经常出现40℃高温天气。要作好防暑降温工作。这个节气雨水多，在“小暑、大暑，淹死老鼠”的谚语，要注意防汛防涝。"],
//            '13' => [
//                "name" => "立秋",
//                "ename" => "the Beginning of Autumn",
//                "time" => "8月7~9日",
//                "sanhou" => "凉风至、白露降、寒蝉鸣。",
//                "content" => "北斗指向西南。太阳黄经为135°。从这一天起秋天开始，秋高气爽，月明风清。此后，气温由最热逐渐下降。"],
//            '14' => [
//                "name" => "处暑",
//                "ename" => "the End of Heat ",
//                "time" => "8月22~24日",
//                "sanhou" => "鹰乃祭鸟、天地始肃、禾乃登。",
//                "content" => "斗指戊。太阳黄经为150°。这时夏季火热已经到头了。暑气就要散了。它是温度下降的一个转折点。是气候变凉的象征，表示暑天终止。"],
//            '15' => [
//                "name" => "白露",
//                "ename" => "White Dew",
//                "time" => "9月7~9日",
//                "sanhou" => "鸿雁来、玄鸟归、群鸟养羞。",
//                "content" => "斗指癸。太阳黄经为165°。天气转凉，地面水汽结露最多。"],
//            '16' => [
//                "name" => "秋分",
//                "ename" => "the Autumn Equinox",
//                "time" => "9月22~24日",
//                "sanhou" => "雷始收声、蛰虫培户、水始涸。",
//                "content" => "斗指已。太阳黄经为180°。秋分这一天同春人一样，阳光几乎直射赤道，昼夜几乎相等。从这一天起，阳光直射位置继续由赤道向南半球推移，北半球开始昼短夜长。依我国旧历的秋季论，这一天刚好是秋季九十天的一半，因而称秋分。但在天文学上规定，北半球的秋天是从秋分开始的。"],
//            '17' => [
//                "name" => "寒露",
//                "ename" => "Cold Dew",
//                "time" => "10月8~10日",
//                "sanhou" => "鸿雁来宾、雀攻大水为蛤、菊有黄花。",
//                "content" => "斗指甲。太阳黄经为195°。白露后，天气转凉，开始出现露水，到了寒露，则露水日多，且气温更低了。所以，有人说，寒是露之气，先白而后寒，是气候将逐渐转冷的意思。而水气则凝成白色露珠。"],
//            '18' => [
//                "name" => "霜降",
//                "ename" => "Frost\'s Descent",
//                "time" => "10月23~24日",
//                "sanhou" => "豺乃祭兽、草木黄落、蛰虫咸俯。",
//                "content" => "太阳黄经为210°。天气已冷，开始有霜冻了，所以叫霜降。"],
//            '19' => [
//                "name" => "立冬",
//                "ename" => "the Beginning of Winter",
//                "time" => "11月7~8日",
//                "sanhou" => "水始冰、地始冻、雉入大水为蜃。",
//                "content" => "太阳黄经为225°。习惯上，我国人民把这一天当作冬季的开始。冬，作为终了之意，是指一年的田间操作结束了，作物收割之后要收藏起来的意思。立冬一过，我国黄河中、下游地区即将结冰，我国各地农民都将陆续地转入农田水利基本建设和其他农事活动中。"],
//            '20' => [
//                "name" => "小雪",
//                "ename" => "Lesser Snow",
//                "time" => "11月22~23日",
//                "sanhou" => "虹藏不见、天气上腾、闭塞而成冬。",
//                "content" => "太阳黄经为240°。气温下降，开始降雪，但还不到大雪纷飞的时节，所以叫小雪。小雪前后，黄河流域开始降雪（南方降雪还要晚两个节气）；而北方，已进入封冻季节。"],
//            '21' => [
//                "name" => "大雪",
//                "ename" => "Greater Snow",
//                "time" => "12月6~8日",
//                "sanhou" => "鴠鸟不鸣、虎始交、荔挺生。",
//                "content" => "太阳黄经为255°。大雪前后，黄河流域一带渐有积雪；而北方，已是“千里冰封，万里雪飘荡”的严冬了。"],
//            '22' => [
//                "name" => "冬至",
//                "ename" => "the Winter Solstice",
//                "time" => "12月21~23日",
//                "sanhou" => "蚯蚓结、麋角解、水泉动。",
//                "content" => "太阳黄经为270°。冬至这一天，阳光几乎直射南回归线，我们北半球白昼最短，黑夜最长，开始进入数九寒天。天文学上规定这一天是北半球冬季的开始。而冬至以后，阳光直射位置逐渐向北移动，北半球的白天就逐渐长了，谚云：吃了冬至面，一天长一线。"],
//            '23' => [
//                "name" => "小寒",
//                "ename" => "Lesser Cold",
//                "time" => "1月5~7日",
//                "sanhou" => "雁北向、鹊始巢、雉始雊。",
//                "content" => "太阳黄经为285°。小寒以后，开始进入寒冷季节。冷气积久而寒，小寒是天气寒冷但还没有到极点的意思。"],
//            '24' => [
//                "name" => "大寒",
//                "ename" => "Greater Cold",
//                "time" => "1月20~21日",
//                "sanhou" => "鸡始乳、鸷鸟厉疾、水泽腹坚。",
//                "content" => "太阳黄经为300°。大寒就是天气寒冷到了极点的意思。大寒前后是一年中最冷的季节。大寒正值三九刚过，四九之初。谚云：“三九四九冰上走”。 "],
//        ];
//        $num=0;
//        foreach ($detailJieQi as $datailjieqi_k => $datailjieqi_v) {
//            $file_dir = 'data/jieqi/' . $num. '.dat';
//            $fp = fopen($file_dir, "r");
//            $content = fread($fp, filesize($file_dir));//读文件
//            fclose($fp);
//            //分行存入数组
//            $arr = explode("\n", $content);
//            $year=date("Y",strtotime($daytime));
//            $datailjieqi_v['time_1']=$arr[$year];
//            $datailjieqi_v['time_2']=date('Ymd',strtotime($arr[$year]));
//            $jieqi[$datailjieqi_v['time_2']]=$datailjieqi_v;
//            $num++;
//        }
//       return $jieqi;
//    }
}
