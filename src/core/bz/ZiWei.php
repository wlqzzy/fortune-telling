<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace FortuneTelling\core\bz;

use FortuneTelling\data\BaZiDb;
use FortuneTelling\data\ZiWeiDb;

class ZiWei
{
    //时辰地支
    private $hourDzNum;
    //性别
    private $sex;
    //农历出生年
    private $lunarBirthYear;
    //农历出生月
    private $lunarBirthMonth;
    //农历出生日
    private $lunarBirthDay;
    //农历出生月是否闰月
    private $isLeapMonth;
    //流年农历年
    private $liuNianLunarYear;
    //出生年干支
    private $birthYearGz;
    //流年年干支
    private $liuNianYearGz;
    //是否已经初始化过基础信息
    private $checkInit = false;
    //紫微命盘
    private $mp;

    /**
     * 初始化用户信息
     *
     * @param int $hourDzNum        时辰地支
     * @param int $sex              性别
     * @param int $lunarBirthYear   农历出生年
     * @param int $lunarBirthMonth  农历出生月
     * @param int $lunarBirthDay    农历出生日
     * @param int $isLeapMonth      农历出生月是否闰月
     * @param int $liuNianLunarYear 流年农历年
     * @return $this
     *
     * @author wlq
     * @since 1.0 2023-09-18
     */
    public function init(
        int $hourDzNum,
        int $sex,
        int $lunarBirthYear,
        int $lunarBirthMonth,
        int $lunarBirthDay,
        int $isLeapMonth = 0,
        int $liuNianLunarYear = 0
    ): self {
        $this->hourDzNum = $hourDzNum;
        $this->sex = $sex;
        $this->lunarBirthYear = $lunarBirthYear;
        $this->lunarBirthMonth = $lunarBirthMonth;
        $this->lunarBirthDay = $lunarBirthDay;
        $this->isLeapMonth = $isLeapMonth;
        $this->liuNianLunarYear = $liuNianLunarYear;
        $this->checkInit = true;
        $this->birthYearGz = $this->getYearGzNum($lunarBirthYear);
        $this->liuNianYearGz = $liuNianLunarYear ? $this->getYearGzNum($liuNianLunarYear) : [];
        if ($this->isLeapMonth) {
            $this->lunarBirthMonth += 1;
        }
        $this->makeMp();
        return $this;
    }

    /**
     * 更改流年年份
     *
     * @param int $liuNianLunarYear
     * @return $this
     *
     * @author wlq
     * @since 1.0 2023-09-18
     */
    public function setLiuNianLunarYear(int $liuNianLunarYear): self
    {
        $this->sex = $liuNianLunarYear;
        $this->liuNianYearGz = $liuNianLunarYear ? $this->getYearGzNum($liuNianLunarYear) : [];
        $this->makeMp();
        return $this;
    }

    /**
     * 校验是否已初始化
     *
     * @return bool
     *
     * @author wlq
     * @since 1.0 2023-09-18
     */
    public function checkInit(): bool
    {
        return $this->checkInit;
    }

    /**
     * 获取基础命盘
     *
     * @return array
     *
     * @author wlq
     * @since 1.0 2023-09-18
     */
    private function getMpBase(): array
    {
        /**
         * 第一步：定宫干
         */
        $sTg = ($this->birthYearGz['tgNum'] + 1) * 2 % 10;
        $sDz = 2;
        $mp = [];//命盘基数
        for ($i = 0; $i < 12; $i++) {
            $mp[$sDz % 12] = [
                'tg' => [
                    'name' => '宫干',
                    'value' => $sTg % 10
                ],
                'dz' => [
                    'name' => '宫支',
                    'value' => $sDz % 12
                ],
                'xc' => [
                    'name' => '星辰',
                    'value' => []
                ]
            ];
            $sTg++;
            $sDz++;
        }
        $this->mp = $mp;
        return $this->mp;
    }

    /**
     * 获取命宫下标
     *
     * @return int
     *
     * @author wlq
     * @since 1.0 2023-09-18
     */
    private function getMgNum(): int
    {
        if (!property_exists($this, 'mgNum')) {
            //命宫确定规则：寅宫起数，正数月数，到达宫起数，倒数时辰地支数
            //下标计算：寅宫【2】 + 月下标【month-1】 - 时辰下标【dzNum】
            $this->mgNum = (2 + $this->lunarBirthMonth - 1 - $this->hourDzNum + 12) % 12;
        }
        return $this->mgNum;
    }

    /**
     * 获取身宫下标
     *
     * @return int
     *
     * @author wlq
     * @since 1.0 2023-09-18
     */
    private function getSgNum(): int
    {
        if (!property_exists($this, 'sgNum')) {
            $this->sgNum = (2 + $this->lunarBirthMonth - 1 + ($this->hourDzNum - 1)) % 12;
        }
        return $this->sgNum;
    }

    /**
     * 获取流年命宫下标
     *
     * @return int
     *
     * @author wlq
     * @since 1.0 2023-09-18
     */
    private function getLnMgNum(): int
    {
        //命宫确定规则：寅宫起数，正数月数，到达宫起数，倒数时辰地支数
        //流年命宫=流年年地支下标
        return $this->liuNianYearGz['dzNum'] ?? -1;
    }

    /**
     * 获取五局信息
     *
     * @return array
     *
     * @author wlq
     * @since 1.0 2023-09-18
     */
    private function getWj(): array
    {
        if (!property_exists($this, 'wj')) {
            $tg_num = floor($this->mp[$this->getMgNum()]['tg']['value'] / 2);
            $dz_num = floor($this->mp[$this->getMgNum()]['dz']['value'] / 2) % 3;
            $this->wj = ZiWeiDb::WU_JU_WX[($tg_num + $dz_num) % 5];
        }
        return $this->wj;
    }

    /**
     * 根据指定宫位设置大运宫位
     *
     * @param int $i
     * @param int $dySort
     *
     * @author wlq
     * @since 1.0 2023-09-18
     */
    private function setDy(int $i, int $dySort)
    {
        $iDySort = $i * $dySort;
        $dyStart = $this->getWj()[1] + $i * 10;
        $dyEnd = $dyStart + 9;
        $this->mp[($this->getMgNum() + 12 + $iDySort) % 12]['dy'] = [
            'name' => '大运',
            'value' => [
                'se' => $dyStart . '~' . $dyEnd,
                's' => $dyStart,
                'e' => $dyEnd
            ]
        ];
    }

    /**
     * 获取大运宫位
     *
     * @param int $dySort
     * @return int
     *
     * @author wlq
     * @since 1.0 2023-09-18
     */
    private function getDyMgNum(int $dySort): int
    {
        if (!property_exists($this, 'dyMgNum')) {
            $age = $this->liuNianLunarYear - $this->lunarBirthYear;
            $dyStart = $age - $age % 10 + $this->getWj()[1];
            if ($age < $dyStart) {
                $dyStart -= 10;
            }
            $this->dyMgNum = ($this->getMgNum() + 12 + $dySort * floor($dyStart / 10)) % 12;
        }
        return $this->dyMgNum;
    }
    /**
     * 根据指定宫位设置宫职宫位
     *
     * @param int $i
     * @param int $mgNumDy
     *
     * @author wlq
     * @since 1.0 2023-09-18
     */
    private function setGongZhi(int $i, int $mgNumDy = -1)
    {
        //本命宫职
        $this->mp[($this->getMgNum() - $i + 12) % 12]['gz'] = [
            'name' => '宫职',
            'value' => [
                'bm' => ZiWeiDb::GONG_ZHI[$i]
            ]
        ];
        //流年宫职
        if ($this->getLnMgNum() > -1) {
            $this->mp[($this->getLnMgNum() - $i + 12) % 12]['gz']['value']['ln'] = ZiWeiDb::GONG_ZHI[$i];
        }
        //大运宫职
        if ($mgNumDy > -1) {
            $this->mp[($mgNumDy - $i + 12) % 12]['gz']['value']['dy'] = ZiWeiDb::GONG_ZHI[$i];
        }
    }

    /**
     * 安星
     *
     *
     * @author wlq
     * @since 1.0 2023-09-18
     */
    private function setXingChen(): void
    {
        /**
         * 一、紫微星系
         */
        //(1)紫微星
        $sNum = ($this->getWj()[1] - $this->lunarBirthDay % $this->getWj()[1]) % 4;
        $sNum = ($sNum % 2) ? (0 - $sNum) : $sNum;
        //紫微星确定规则：寅宫起数，正数天数除以五局局数的商值（向上取整），停留位置再起数天数与五局局数倍数的最小差值（差值为单数则倒数，为双数则正数）
        //算法：寅宫下标 + 商【ceil(day/五局局数)】 + 差值【sNum】
        $zwMpi = 2 + ceil($this->lunarBirthDay / $this->getWj()[1]) + $sNum + 12;
        //(2)紫微星
        $this->mp[$zwMpi % 12]['xc']['value'][] = [
            'xc' => ZiWeiDb::INDEX_ZI_WEI,
            'hyx' => ''
        ];
        //(3)天机
        $this->mp[($zwMpi - 1) % 12]['xc']['value'][] = [
            'xc' => ZiWeiDb::INDEX_TIAN_JI,
            'hyx' => ''
        ];
        //(4)太阳
        $this->mp[($zwMpi - 3) % 12]['xc']['value'][] = [
            'xc' => ZiWeiDb::INDEX_TAI_YANG,
            'hyx' => ''
        ];
        //(5)武曲
        $this->mp[($zwMpi  - 4) % 12]['xc']['value'][] = [
            'xc' => ZiWeiDb::INDEX_WU_QU,
            'hyx' => ''
        ];
        //(6)天同
        $this->mp[($zwMpi - 5) % 12]['xc']['value'][] = [
            'xc' => ZiWeiDb::INDEX_TIAN_TONG,
            'hyx' => ''
        ];
        //(7)廉贞
        $this->mp[($zwMpi - 8) % 12]['xc']['value'][] = [
            'xc' => ZiWeiDb::INDEX_LIAN_ZHEN,
            'hyx' => ''
        ];
        /**
         * 二、天府星系
         */
        //(1)天府星
        $tfMpi = 40 - $zwMpi;
        $this->mp[$tfMpi % 12]['xc']['value'][] = [
            'xc' => ZiWeiDb::INDEX_TIAN_FU,
            'hyx' => ''
        ];
        //(2)太阴
        $this->mp[($tfMpi + 1) % 12]['xc']['value'][] = [
            'xc' => ZiWeiDb::INDEX_TAI_YIN,
            'hyx' => ''
        ];
        //(3)贪狼
        $this->mp[($tfMpi + 2) % 12]['xc']['value'][] = [
            'xc' => ZiWeiDb::INDEX_TAN_LANG,
            'hyx' => ''
        ];
        //(4)巨门
        $this->mp[($tfMpi + 3) % 12]['xc']['value'][] = [
            'xc' => ZiWeiDb::INDEX_JU_MEN,
            'hyx' => ''
        ];
        //(5)天相
        $this->mp[($tfMpi + 4) % 12]['xc']['value'][] = [
            'xc' => ZiWeiDb::INDEX_TIAN_XIANG,
            'hyx' => ''
        ];
        //(6)天梁
        $this->mp[($tfMpi + 5) % 12]['xc']['value'][] = [
            'xc' => ZiWeiDb::INDEX_TIAN_LIANG,
            'hyx' => ''
        ];
        //(7)七杀
        $this->mp[($tfMpi + 6) % 12]['xc']['value'][] = [
            'xc' => ZiWeiDb::INDEX_QI_SHA,
            'hyx' => ''
        ];
        //(8)破军
        $this->mp[($tfMpi + 10) % 12]['xc']['value'][] = [
            'xc' => ZiWeiDb::INDEX_PO_JUN,
            'hyx' => ''
        ];
        /**
         * 三、安年星系
         */
        $anXc = [
            ZiWeiDb::INDEX_LU_CUN,
            ZiWeiDb::INDEX_YANG_REN,
            ZiWeiDb::INDEX_TUO_LUO,
            ZiWeiDb::INDEX_TIAN_KUI,
            ZiWeiDb::INDEX_TIAN_YUE,
            ZiWeiDb::INDEX_HONG_LUAN,
            ZiWeiDb::INDEX_TIAN_XI
        ];
        $anTgArr = [
            [2, 3, 1, 1, 7],
            [3, 4, 2, 0, 8],
            [5, 6, 4, 11, 9],
            [6, 7, 5, 11, 9],
            [5, 6, 4, 1, 7],
            [6, 7, 5, 0, 8],
            [8, 9, 7, 1, 7],
            [9, 10, 8, 6, 2],
            [11, 0, 10, 3, 5],
            [0, 1, 11, 3, 5]
        ];
        $anDzArr = [
            [3, 9], [2, 8], [1, 7], [0, 6], [11, 5], [10, 4], [9, 3], [8, 2], [7, 1], [6, 0], [5, 11], [4, 10]
        ];
        $anXc_0_4 = $anTgArr[$this->birthYearGz['tgNum']];
        $anXc_5_6 = $anDzArr[$this->birthYearGz['dzNum']];
        foreach ($anXc_0_4 as $k => $v) {
            $this->mp[$v]['xc']['value'][] = [
                'xc' => $anXc[$k],
                'hyx' => ''
            ];
        }
        foreach ($anXc_5_6 as $k => $v) {
            $this->mp[$v]['xc']['value'][] = [
                'xc' => $anXc[$k + 5],
                'hyx' => ''
            ];
        }
        /**
         * 四、安月星系
         */
        //(1)左辅
        $this->mp[(BaZiDb::DZ_NUM['辰'] + ($this->lunarBirthMonth - 1) + 12) % 12]['xc']['value'][] = [

            'xc' => ZiWeiDb::INDEX_ZUO_FU,

            'hyx' => ''
        ];
        //(2)右弼
        $this->mp[(BaZiDb::DZ_NUM['戌'] - ($this->lunarBirthMonth - 1) + 12) % 12]['xc']['value'][] = [

            'xc' => ZiWeiDb::INDEX_YOU_BI,

            'hyx' => ''
        ];
        //(3)天刑
        $this->mp[(BaZiDb::DZ_NUM['酉'] + ($this->lunarBirthMonth - 1) + 12) % 12]['xc']['value'][] = [
            'xc' => ZiWeiDb::INDEX_TIAN_XING,
            'hyx' => ''
        ];
        //(4)天姚
        $this->mp[(BaZiDb::DZ_NUM['丑'] + ($this->lunarBirthMonth - 1) + 12) % 12]['xc']['value'][] = [
            'xc' => ZiWeiDb::INDEX_TIAN_YAO,
            'hyx' => ''
        ];
        //(5)天马
        $this->mp[(BaZiDb::DZ_NUM['申'] - (($this->lunarBirthMonth - 1) % 4) * 3 + 12) % 12]['xc']['value'][] = [
            'xc' => ZiWeiDb::INDEX_TIAN_MA,
            'hyx' => ''
        ];
        /**
         * 五：安时星系
         */
        //(1)文昌
        $this->mp[(BaZiDb::DZ_NUM['戌'] - $this->hourDzNum + 12) % 12]['xc']['value'][] = [
            'xc' => ZiWeiDb::INDEX_WEN_CHANG,
            'hyx' => ''
        ];
        //(2)文曲
        $this->mp[(BaZiDb::DZ_NUM['辰'] + $this->hourDzNum + 12) % 12]['xc']['value'][] = [
            'xc' => ZiWeiDb::INDEX_WEN_QU,
            'hyx' => ''
        ];
        //(3)火、(4)铃
        switch ($this->birthYearGz['dzNum'] % 4) {
            case 0:
                $this->mp[(BaZiDb::DZ_NUM['寅'] + $this->hourDzNum) % 12]['xc']['value'][] = [
                    'xc' => ZiWeiDb::INDEX_HUO,
                    'hyx' => ''
                ];
                $this->mp[(BaZiDb::DZ_NUM['戌'] + $this->hourDzNum) % 12]['xc']['value'][] = [
                    'xc' => ZiWeiDb::INDEX_LING,
                    'hyx' => ''
                ];
                break;
            case 1:
                $this->mp[(BaZiDb::DZ_NUM['卯'] + $this->hourDzNum) % 12]['xc']['value'][] = [
                    'xc' => ZiWeiDb::INDEX_HUO,
                    'hyx' => ''
                ];
                $this->mp[(BaZiDb::DZ_NUM['戌'] + $this->hourDzNum) % 12]['xc']['value'][] = [
                    'xc' => ZiWeiDb::INDEX_LING,
                    'hyx' => ''
                ];
                break;
            case 2:
                $this->mp[(BaZiDb::DZ_NUM['丑'] + $this->hourDzNum) % 12]['xc']['value'][] = [
                    'xc' => ZiWeiDb::INDEX_HUO,
                    'hyx' => ''
                ];
                $this->mp[(BaZiDb::DZ_NUM['卯'] + $this->hourDzNum) % 12]['xc']['value'][] = [
                    'xc' => ZiWeiDb::INDEX_LING,
                    'hyx' => ''
                ];
                break;
            case 3:
                $this->mp[(BaZiDb::DZ_NUM['酉'] + $this->hourDzNum) % 12]['xc']['value'][] = [
                    'xc' => ZiWeiDb::INDEX_HUO,
                    'hyx' => ''
                ];
                $this->mp[(BaZiDb::DZ_NUM['戌'] + $this->hourDzNum) % 12]['xc']['value'][] = [
                    'xc' => ZiWeiDb::INDEX_LING,
                    'hyx' => ''
                ];
                break;
        }
    }

    /**
     * 星辰化四耀星
     *
     * @param int $i
     * @param int $mgDyTg
     *
     * @author wlq
     * @since 1.0 2023-09-18
     */
    private function setHyx(int $i, int $mgDyTg = -1): void
    {
        $tg_num = $this->mp[$i]['tg']['value'];
        $tg_num_ln = $this->liuNianYearGz ? $this->liuNianYearGz['tgNum'] : -1;
        foreach ($this->mp[$i]['xc']['value'] as $k => $v) {
            //耀星
            $this->mp[$i]['xc']['value'][$k]['hyx'] = ZiWeiDb::XING_TO_SI_YAO[$tg_num][$v['xc']] ?? '';
            //耀星流年
            $this->mp[$i]['xc']['value'][$k]['hyx_ln'] = ZiWeiDb::XING_TO_SI_YAO[$tg_num_ln][$v['xc']] ?? '';
            //耀星大运
            $this->mp[$i]['xc']['value'][$k]['hyx_dy'] = ZiWeiDb::XING_TO_SI_YAO[$mgDyTg][$v['xc']] ?? '';
            //星辰旺度
            $this->mp[$i]['xc']['value'][$k]['wd'] = ZiWeiDb::WANG_DU[ZiWeiDb::TGXC_TO_WD[$v['xc']][$i]];
        }
    }

    /**
     * 紫微命盘
     *
     * @return array
     *
     * @author wlq
     * @since 1.0 2023-09-18
     */
    private function makeMp(): array
    {
        /**
         * 第一步：定宫干
         */
        $this->getMpBase();
        /**
         * 第二步：安星
         */
        $this->setXingChen();
        //数大运顺序
        $tgYinYang = $this->mp[$this->getMgNum()]['tg']['value'] % 2 ? -1 : 1;
        $sexYinYang = $this->sex ? 1 : -1;
        $dySort = $tgYinYang * $sexYinYang;
        //获取命宫大运位置
        $mgNumDy = $this->getDyMgNum($dySort);
        for ($i = 0; $i < 10; $i++) {
            /**
             * 第三步：定大运
             */
            $this->setDy($i, $dySort);
            //(2)定宫职
            $this->setGongZhi($i, $mgNumDy);
            /**
             * 第四步：化四曜星
             */
            $this->setHyx($i, $mgNumDy);
        }
        return $this->mp;
    }

    /**
     * 获取年干支下标
     *
     * @param $year
     * @return int[]
     *
     * @author wlq
     * @since 1.0 2023-09-18
     */
    private function getYearGzNum($year): array
    {
        $tgNum = ($year - 4) % 10;
        $dzNum = ($year - 4) % 12;
        return ['tgNum' => $tgNum,'dzNum' => $dzNum];
    }

    /**
     * 获取命盘
     *
     * @return mixed
     *
     * @author wlq
     * @since 1.0 2024-05-31
     */
    public function getMp()
    {
        return $this->mp;
    }
    /**
     * 获取命宫信息
     *
     * @return array
     *
     * @author wlq
     * @since 1.0 2023-09-18
     */
    public function getMpMg(): array
    {
        return $this->mp[$this->getMgNum()] ?? [];
    }

    /**
     * 获取身宫信息
     *
     * @return array
     *
     * @author wlq
     * @since 1.0 2023-09-18
     */
    public function getMpSg(): array
    {
        return $this->mp[$this->getSgNum()] ?? [];
    }

    /**
     * 获取流年命宫信息
     *
     * @return array
     *
     * @author wlq
     * @since 1.0 2023-09-18
     */
    public function getMpLnMgm(): array
    {
        return $this->mp[$this->getLnMgNum()] ?? [];
    }
}
