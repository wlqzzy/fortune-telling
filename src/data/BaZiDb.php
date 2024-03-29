<?php

namespace FortuneTelling\data;

class BaZiDb
{
    //天干数组
    public const TG_ARR = ['甲', '乙', '丙', '丁', '戊', '己', '庚', '辛', '壬', '癸'];
    //天干排序数组-数字
    public const TG_NUM = [
        '甲' => 0,
        '乙' => 1,
        '丙' => 2,
        '丁' => 3,
        '戊' => 4,
        '己' => 5,
        '庚' => 6,
        '辛' => 7,
        '壬' => 8,
        '癸' => 9
    ];
    //地支数组
    public const DZ_ARR = ['子', '丑', '寅', '卯', '辰', '巳', '午', '未', '申', '酉', '戌', '亥'];
    //地支排序数组-数字
    public const DZ_NUM = [
        '子' => 0,
        '丑' => 1,
        '寅' => 2,
        '卯' => 3,
        '辰' => 4,
        '巳' => 5,
        '午' => 6,
        '未' => 7,
        '申' => 8,
        '酉' => 9,
        '戌' => 10,
        '亥' => 11
    ];
    //生肖数组
    public const SX_ARR = ['鼠', '牛', '虎', '兔', '龙', '蛇', '马', '羊', '猴', '鸡', '狗', '猪'];
    //生肖排序数组-数字
    public const SX_NUM = [
        '鼠' => 0,
        '牛' => 1,
        '虎' => 2,
        '兔' => 3,
        '龙' => 4,
        '蛇' => 5,
        '马' => 6,
        '羊' => 7,
        '猴' => 8,
        '鸡' => 9,
        '狗' => 10,
        '猪' => 11
    ];
    //五行排序数字
    public const WX_NUM = ['金' => 1, '木' => 2, '土' => 3, '水' => 4, '火' => 5];
    //五行排序数组
    public const WX_ARR = ['金', '木', '土', '水', '火'];
    //干支数字-排序数组
    public const GZ_ORDER = [
        11,22,33,44,55,66,77,88,99,1010,111,212,
        31,42,53,64,75,86,97,108,19,210,311,412,
        51,62,73,84,95,106,17,28,39,410,511,612,
        71,82,93,104,15,26,37,48,59,610,711,812,
        91,102,13,24,35,46,57,68,79,810,911,1012
    ];
}