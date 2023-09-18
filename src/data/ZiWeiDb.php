<?php

namespace FortuneTelling\data;

class ZiWeiDb
{
    //紫微名盘-宫职
    public const GONG_ZHI = ['命', '兄弟', '夫妻', '子女', '财帛', '疾厄', '迁移', '奴仆', '官禄', '田宅', '德福', '父母'];
    //紫微命盘-五局五行
    public const WU_JU_WX = [['金', 4, 13], ['水', 2, 11], ['火', 6, 15], ['土', 5, 14], ['木', 3, 12]];
    //紫薇命盘-星辰
    public const XING_CHEN = [
        //紫微星系
        0 => '紫微', 1 => '天机', 2 => '太阳', 3 => '武曲', 4 => '天同', 5 => '廉贞',
        //天府星系
        6 => '天府', 7 => '太阴', 8 => '贪狼', 9 => '巨门', 10 => '天相', 11 => '天梁', 12 => '七杀', 13 => '破军',
        //安年星系
        14 => '禄存', 15 => '羊刃', 16 => '陀罗', 17 => '天魁', 18 => '天钺', 19 => '红鸾', 20 => '天喜',
        //安月星系
        21 => '左辅', 22 => '右弼', 23 => '天刑', 24 => '天姚', 25 => '天马',
        //安时星系
        26 => '文昌', 27 => '文曲', 28 => '火', 29 => '铃',
    ];
    public const XING_TO_SI_YAO = [
        [5 => '禄', 13 => '权', 3 => '科', 2 => '忌'],
        [1 => '禄', 11 => '权', 0 => '科', 7 => '忌'],
        [4 => '禄', 1 => '权', 26 => '科', 5 => '忌'],
        [7 => '禄', 4 => '权', 1 => '科', 9 => '忌'],
        [8 => '禄', 7 => '权', 22 => '科', 1 => '忌'],
        [3 => '禄', 8 => '权', 11 => '科', 27 => '忌'],
        [2 => '禄', 3 => '权', 7 => '科', 4 => '忌'],
        [9 => '禄', 2 => '权', 27 => '科', 26 => '忌'],
        [11 => '禄', 0 => '权', 21 => '科', 3 => '忌'],
        [13 => '禄', 9 => '权', 7 => '科', 8 => '忌']
    ];
    //天干下标星辰下标转旺度下标
    public const TGXC_TO_WD = [
        [4,1,2,2,3,2,1,1,2,2,3,2],//紫微
        [1,5,3,2,4,4,1,5,3,2,4,4],//天机
        [5,5,2,1,2,2,2,3,3,4,5,5],//太阳
        [2,1,3,4,1,4,2,1,3,4,1,4],//武曲
        [2,5,4,4,4,1,5,5,2,4,4,1],//天同
        [4,4,1,4,4,5,4,4,1,4,4,5],//廉贞
        [1,1,1,3,1,3,2,1,3,2,1,3],//天府
        [1,1,2,5,5,5,5,5,4,2,2,1],//太阴
        [2,1,4,4,1,5,2,1,4,4,1,5],//贪狼
        [2,5,1,1,5,2,2,5,1,1,5,2],//巨门
        [1,1,1,5,3,3,1,3,1,5,3,3],//天相
        [1,2,1,1,1,5,1,2,5,3,1,5],//天梁
        [2,1,1,2,1,4,2,1,1,2,1,4],//七杀
        [1,2,3,5,2,4,1,2,3,5,2,4],//破军
        [1,0,1,1,0,1,1,0,1,1,0,1],//禄存
        [5,1,0,5,1,0,5,1,0,5,1,0],//羊刃
        [0,1,5,0,1,5,0,1,5,0,1,5],//陀罗
        [2,2,0,1,0,0,1,0,0,0,0,2],//天魁
        [0,0,2,0,0,2,0,2,1,1,0,0],//天钺
        [5,1,1,1,1,5,5,5,5,5,1,1],//红鸾
        [0,0,0,0,0,0,0,0,0,0,0,0],//天喜
        [6,6,6,6,6,6,6,6,6,6,6,6],//左辅
        [6,6,6,6,6,6,6,6,6,6,6,6],//右弼
        [4,5,1,1,4,4,4,5,4,1,1,4],//天刑
        [4,5,4,1,4,4,4,5,4,1,1,1],//天姚
        [0,0,2,0,0,4,0,0,2,0,0,4],//天马
        [3,1,5,4,3,1,5,4,3,1,5,4],//文昌
        [3,1,5,2,3,1,5,2,3,1,5,2],//文曲
        [5,3,1,4,5,3,1,4,5,3,1,4],//火
        [5,3,1,4,5,3,1,4,5,3,1,4],//铃
    ];
    //旺度
    public const WANG_DU = [
        0 => '',
        1 => '庙',
        2 => '旺',
        3 => '得地',
        4 => '平',
        5 => '陷',
        6 => '吉'
    ];

    //紫微星系
    //紫微
    public const INDEX_ZI_WEI = 0;
    //天机
    public const INDEX_TIAN_JI = 1;
    //太阳
    public const INDEX_TAI_YANG = 2;
    //武曲
    public const INDEX_WU_QU = 3;
    //天同
    public const INDEX_TIAN_TONG = 4;
    //廉贞
    public const INDEX_LIAN_ZHEN = 5;

    //天府星系
    //天府
    public const INDEX_TIAN_FU = 6;
    //太阴
    public const INDEX_TAI_YIN = 7;
    //贪狼
    public const INDEX_TAN_LANG = 8;
    //巨门
    public const INDEX_JU_MEN = 9;
    //天相
    public const INDEX_TIAN_XIANG = 10;
    //天梁
    public const INDEX_TIAN_LIANG = 11;
    //七杀
    public const INDEX_QI_SHA = 12;
    //破军
    public const INDEX_PO_JUN = 13;

    //安年星系
    //禄存
    public const INDEX_LU_CUN = 14;
    //羊刃
    public const INDEX_YANG_REN = 15;
    //陀罗
    public const INDEX_TUO_LUO = 16;
    //天魁
    public const INDEX_TIAN_KUI = 17;
    //天钺
    public const INDEX_TIAN_YUE = 18;
    //红鸾
    public const INDEX_HONG_LUAN = 19;
    //天喜
    public const INDEX_TIAN_XI = 20;

    //安月星系
    //左辅
    public const INDEX_ZUO_FU = 21;
    //右弼
    public const INDEX_YOU_BI = 22;
    //天刑
    public const INDEX_TIAN_XING = 23;
    //天姚
    public const INDEX_TIAN_YAO = 24;
    //天马
    public const INDEX_TIAN_MA = 25;

    //安时星系
    //文昌
    public const INDEX_WEN_CHANG = 26;
    //文曲
    public const INDEX_WEN_QU = 27;
    //火
    public const INDEX_HUO = 28;
    //铃
    public const INDEX_LING = 29;
}