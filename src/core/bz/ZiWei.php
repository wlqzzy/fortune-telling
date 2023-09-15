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

class ZiWei
{

    /**
     * 紫微命盘
     * @param       array       $bz                 八字base算法result['birth_bz']['value']
     * @param       string      $birth_nongli       八字base算法result['birth_nl_no']['value']
     * @param       string      $birth_nongli_ly    八字base算法result['birth_nl_no']['value'](流年【指定年】农历)
     * @param       int         $is_ry              八字base算法result['birth_nl_is_ry']['value']
     * @return      array
     */
    public function get_mp($bz, $sex, $birth_nongli,$is_ry=0,$birth_nongli_ly='')
    {
        $year_gz=$this->getYearGzNum(date('Y',strtotime($birth_nongli)));
        $year_gz_ly=$birth_nongli_ly?$this->getYearGzNum(date('Y',strtotime($birth_nongli_ly))):'';
        $age=date('Y',strtotime($birth_nongli_ly))-date('Y',strtotime($birth_nongli));
        /**
         * 第一步：定宫干
         */
        $start_gg = [
            '甲' => 2,
            '乙' => 4,
            '丙' => 6,
            '丁' => 8,
            '戊' => 0,
            '己' => 2,
            '庚' => 4,
            '辛' => 6,
            '壬' => 8,
            '癸' => 0,
        ];
        $stg = $start_gg[$year_gz['tg']];
        $sdz = 2;
        $mp = [];//命盘基数
        for ($i = 0; $i < 12; $i++) {
            $mp[($sdz + $i) % 12] = [
                'tgdz' => ['name' => '宫干宫支', 'value' => BaZiDb::TG_ARR[($stg + $i) % 10] . BaZiDb::DZ_ARR[($sdz + $i) % 12]],
                'tg' => ['name' => '宫干', 'value' => BaZiDb::TG_ARR[($stg + $i) % 10]],
                'dz' => ['name' => '宫支', 'value' => BaZiDb::DZ_ARR[($sdz + $i) % 12]],
                'xc' => ['name' => '星辰', 'value' => []]
            ];
        }

        /**
         * 第二步：定宫职
         */
        //(1)定命宫
        $birth_month = date('m', strtotime($birth_nongli));
        if($is_ry){
            $birth_month+=1;
        }
        $mg_num = (2 + $birth_month - (BaZiDb::DZ_NUM[$bz['hour']['dz']] - 1) - 1 + 12) % 12;
        $mg_num_ly =$year_gz_ly?(BaZiDb::DZ_NUM[$year_gz_ly['dz']]-1):'';


        /**
         * 第三步：定五局
         */
        $mg = $mp[$mg_num];
        $tg_num = ceil(BaZiDb::TG_NUM[$mg['tg']['value']] / 2) - 1;
        $dz_num = floor((BaZiDb::DZ_NUM[$mg['dz']['value']] - 1) / 2) % 3;
        $mp['wj'] = ['name' => '五局', 'value' => BaZiDb::WU_JU_WX[BaZiDb::WU_JU[$tg_num][$dz_num]]];
        /**
         * 第四步：定大运
         */
        $tgyy = BaZiDb::TG_NUM[$mg['tg']['value']] % 2 ? 1 : -1;
        $sexyy = $sex ? 1 : -1;
        $dys = $mp['wj']['value'][1];
        $dye = $mp['wj']['value'][2];
        $mg_num_dy='';
        for ($i = 0; $i < 10; $i++) {
            $sort = $i * $tgyy * $sexyy;
            $mp[($mg_num + 12 + $sort) % 12]['dy'] = ['name' => '大运', 'value' => ['se' => ($dys + 10 * $i) . '~' . ($dye + 10 * $i), 's' => ($dys + 10 * $i), 'e' => ($dye + 10 * $i)]];
            if($age>($dys + 10 * $i) && $age<($dye + 10 * $i)){
                $mg_num_dy=($mg_num + 12 + $sort) % 12;
            }
        }
        //(2)定宫职
        for ($i = 0; $i < 12; $i++) {
            $mp[($mg_num - $i+12) % 12]['gz'] = ['name' => '宫职', 'value' => ['bm'=>BaZiDb::GONG_ZHI[$i]]];//本命宫职
        }
        if($year_gz_ly){
            for ($i = 0; $i < 12; $i++) {
                if($mg_num_ly!==''){
                    $mp[($mg_num_ly - $i+12) % 12]['gz']['value']['ln']=BaZiDb::GONG_ZHI[$i];//流年宫职
                }
                if($mg_num_dy!==''){
                    $mp[($mg_num_dy - $i+12) % 12]['gz']['value']['dy']=BaZiDb::GONG_ZHI[$i];//大运宫职
                }
            }
        }
        /**
         * 第五步：安星
         */
        /**
         * 一、紫微星系
         */
        //(1)紫微星
        $bd = date('d', strtotime($birth_nongli));
        $cs = ceil($bd / $mp['wj']['value'][1]);
        $sort = ($mp['wj']['value'][1] * $cs - $bd) % 2 ? -1 * ($mp['wj']['value'][1] * $cs - $bd) : 1 * ($mp['wj']['value'][1] * $cs - $bd);
        $mpi = (BaZiDb::DZ_NUM['寅'] + (($cs + $sort) - 1) - 1 + 12) % 12;
        //(2)紫微星
        $mp[$mpi]['xc']['value'][] = ['xc' => '紫微', 'hyx' => ''];
        //(3)天机
        $mp[($mpi + 12 - 1) % 12]['xc']['value'][] = ['xc' => '天机', 'hyx' => ''];
        //(4)太阳
        $mp[($mpi + 12 - 3) % 12]['xc']['value'][] = ['xc' => '太阳', 'hyx' => ''];
        //(5)武曲
        $mp[($mpi + 12 - 4) % 12]['xc']['value'][] = ['xc' => '武曲', 'hyx' => ''];
        //(6)天同
        $mp[($mpi + 12 - 5) % 12]['xc']['value'][] = ['xc' => '天同', 'hyx' => ''];
        //(7)廉贞
        $mp[($mpi + 12 - 8) % 12]['xc']['value'][] = ['xc' => '廉贞', 'hyx' => ''];
        /**
         * 二、天府星系
         */
        //(1)天府星
        $zw_tf = [4, 3, 2, 1, 0, 11, 10, 9, 8, 7, 6, 5];
        $tf_mpi = $zw_tf[$mpi];
        $mp[$tf_mpi]['xc']['value'][] = ['xc' => '天府', 'hyx' => ''];
        //(2)太阴
        $mp[($tf_mpi + 1) % 12]['xc']['value'][] = ['xc' => '太阴', 'hyx' => ''];
        //(3)贪狼
        $mp[($tf_mpi + 2) % 12]['xc']['value'][] = ['xc' => '贪狼', 'hyx' => ''];
        //(4)巨门
        $mp[($tf_mpi + 3) % 12]['xc']['value'][] = ['xc' => '巨门', 'hyx' => ''];
        //(5)天相
        $mp[($tf_mpi + 4) % 12]['xc']['value'][] = ['xc' => '天相', 'hyx' => ''];
        //(6)天梁
        $mp[($tf_mpi + 5) % 12]['xc']['value'][] = ['xc' => '天梁', 'hyx' => ''];
        //(7)七杀
        $mp[($tf_mpi + 6) % 12]['xc']['value'][] = ['xc' => '七杀', 'hyx' => ''];
        //(8)破军
        $mp[($tf_mpi + 10) % 12]['xc']['value'][] = ['xc' => '破军', 'hyx' => ''];
        /**
         * 三、安年星系
         */
        $anxc = ['禄存', '羊刃', '陀罗', '天魁', '天钺', '红鸾', '天喜'];
        $antg_arr = [
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
        $andz_arr = [
            [3, 9], [2, 8], [1, 7], [0, 6], [11, 5], [10, 4], [9, 3], [8, 2], [7, 1], [6, 0], [5, 11], [4, 10]
        ];
        $anxc_0_4 = $antg_arr[BaZiDb::TG_NUM[$year_gz['tg']] - 1];
        $anxc_5_6 = $andz_arr[BaZiDb::DZ_NUM[$year_gz['dz']] - 1];
        foreach ($anxc_0_4 as $k => $v) {
            $mp[$v]['xc']['value'][] = ['xc' => $anxc[$k], 'hyx' => ''];
        }
        foreach ($anxc_5_6 as $k => $v) {
            $mp[$v]['xc']['value'][] = ['xc' => $anxc[$k + 5], 'hyx' => ''];
        }
        /**
         * 四、安月星系
         */
        //(1)左辅
        $mp[((BaZiDb::DZ_NUM['辰'] - 1) + ($birth_month - 1) + 12) % 12]['xc']['value'][] = ['xc' => '左辅', 'hyx' => ''];
        //(2)右弼
        $mp[((BaZiDb::DZ_NUM['戌'] - 1) - ($birth_month - 1) + 12) % 12]['xc']['value'][] = ['xc' => '右弼', 'hyx' => ''];
        //(3)天刑
        $mp[((BaZiDb::DZ_NUM['酉'] - 1) + ($birth_month - 1) + 12) % 12]['xc']['value'][] = ['xc' => '天刑', 'hyx' => ''];
        //(4)天姚
        $mp[((BaZiDb::DZ_NUM['丑'] - 1) + ($birth_month - 1) + 12) % 12]['xc']['value'][] = ['xc' => '天姚', 'hyx' => ''];
        //(5)天马
        switch (($birth_month - 1) % 4) {
            case 0:
                $mp[BaZiDb::DZ_NUM['申'] - 1]['xc']['value'][] = ['xc' => '天马', 'hyx' => ''];
                break;
            case 1:
                $mp[BaZiDb::DZ_NUM['巳'] - 1]['xc']['value'][] = ['xc' => '天马', 'hyx' => ''];
                break;
            case 2:
                $mp[BaZiDb::DZ_NUM['寅'] - 1]['xc']['value'][] = ['xc' => '天马', 'hyx' => ''];
                break;
            case 3:
                $mp[BaZiDb::DZ_NUM['亥'] - 1]['xc']['value'][] = ['xc' => '天马', 'hyx' => ''];
                break;
        }
        /**
         * 五：安时星系
         */
        $hour_1 = BaZiDb::DZ_NUM[$bz['hour']['dz']] - 1;
        //(1)文昌
        $mp[((BaZiDb::DZ_NUM['戌'] - 1) - $hour_1 + 12) % 12]['xc']['value'][] = ['xc' => '文昌', 'hyx' => ''];
        //(2)文曲
        $mp[((BaZiDb::DZ_NUM['辰'] - 1) + $hour_1 + 12) % 12]['xc']['value'][] = ['xc' => '文曲', 'hyx' => ''];
        //(3)火、(4)铃
        switch ((BaZiDb::DZ_NUM[$year_gz['dz']] - 1) % 4) {
            case 0:
                $mp[(BaZiDb::DZ_NUM['寅'] - 1 + $hour_1) % 12]['xc']['value'][] = ['xc' => '火', 'hyx' => ''];
                $mp[(BaZiDb::DZ_NUM['戌'] - 1 + $hour_1) % 12]['xc']['value'][] = ['xc' => '铃', 'hyx' => ''];
                break;
            case 1:
                $mp[(BaZiDb::DZ_NUM['卯'] - 1 + $hour_1) % 12]['xc']['value'][] = ['xc' => '火', 'hyx' => ''];
                $mp[(BaZiDb::DZ_NUM['戌'] - 1 + $hour_1) % 12]['xc']['value'][] = ['xc' => '铃', 'hyx' => ''];
                break;
            case 2:
                $mp[(BaZiDb::DZ_NUM['丑'] - 1 + $hour_1) % 12]['xc']['value'][] = ['xc' => '火', 'hyx' => ''];
                $mp[(BaZiDb::DZ_NUM['卯'] - 1 + $hour_1) % 12]['xc']['value'][] = ['xc' => '铃', 'hyx' => ''];
                break;
            case 3:
                $mp[(BaZiDb::DZ_NUM['酉'] - 1 + $hour_1) % 12]['xc']['value'][] = ['xc' => '火', 'hyx' => ''];
                $mp[(BaZiDb::DZ_NUM['戌'] - 1 + $hour_1) % 12]['xc']['value'][] = ['xc' => '铃', 'hyx' => ''];
                break;
        }
        /**
         * 第六步：化四曜星
         */
        $shyx = [
            ['廉贞' => '禄', '破军' => '权', '武曲' => '科', '太阳' => '忌'],
            ['天机' => '禄', '天梁' => '权', '紫微' => '科', '太阴' => '忌'],
            ['天同' => '禄', '天机' => '权', '文昌' => '科', '廉贞' => '忌'],
            ['太阴' => '禄', '天同' => '权', '天机' => '科', '巨门' => '忌'],
            ['贪狼' => '禄', '太阴' => '权', '右弼' => '科', '天机' => '忌'],
            ['武曲' => '禄', '贪狼' => '权', '天梁' => '科', '文曲' => '忌'],
            ['太阳' => '禄', '武曲' => '权', '太阴' => '科', '天同' => '忌'],
            ['巨门' => '禄', '太阳' => '权', '文曲' => '科', '文昌' => '忌'],
            ['天梁' => '禄', '紫微' => '权', '左辅' => '科', '武曲' => '忌'],
            ['破军' => '禄', '巨门' => '权', '太阴' => '科', '贪狼' => '忌']
        ];
        $mp['seg'] = ['name' => '十二宫', 'value' => ''];
        for ($i = 0; $i < 12; $i++) {
            $tg_num = BaZiDb::TG_NUM[$mp[$i]['tg']['value']] - 1;
            $tg_num_ln = $year_gz_ly?BaZiDb::TG_NUM[$year_gz_ly['tg']] - 1:'';
            $tg_num_dy = isset($mp[$mg_num_dy])?BaZiDb::TG_NUM[$mp[$mg_num_dy]['tg']['value']] - 1:'';
            foreach ($mp[$i]['xc']['value'] as $k => $v) {
                $mp[$i]['xc']['value'][$k]['hyx'] = isset($shyx[$tg_num][$v['xc']]) ? $shyx[$tg_num][$v['xc']] : '';
                $mp[$i]['xc']['value'][$k]['hyx_ln'] = isset($shyx[$tg_num_ln][$v['xc']]) ? $shyx[$tg_num_ln][$v['xc']] : '';
                $mp[$i]['xc']['value'][$k]['hyx_dy'] = isset($shyx[$tg_num_dy][$v['xc']]) ? $shyx[$tg_num_dy][$v['xc']] : '';
                $mp[$i]['xc']['value'][$k]['wd'] = $this->xc_wd($v['xc'],$i);
            }
            $mp['seg']['value'][$i] = $mp[$i];
            unset($mp[$i]);
        }
        //(3)定身宫
        $sg_num = (2 + $birth_month + (BaZiDb::DZ_NUM[$bz['hour']['dz']] - 1) - 1)%12;
        $mp['sg'] = ['name' => '身宫', 'value' => $mp['seg']['value'][$sg_num]];
        $mp['mg'] = ['name' => '命宫', 'value' => $mp['seg']['value'][$mg_num]];
        return $mp;
    }
    //获取年干支下标
    public function getYearGzNum($year): array
    {
        $tgNum = ($year - 4) % 10;
        $dzNum = ($year - 4) % 12;
        return ['tgNum' => $tgNum,'dzNum' => $dzNum];
    }
    //星辰旺度
    public function xc_wd($xc,$gw){
        $wd=[0=>'',1=>'庙',2=>'旺',3=>'得地',4=>'平',5=>'陷',6=>'吉'];
        $xc_no_data=[
            '紫微'=>0,'天机'=>1,'太阳'=>2,'武曲'=>3,'天同'=>4,'廉贞'=>5,
            '天府'=>6,'太阴'=>7,'贪狼'=>8,'巨门'=>9,'天相'=>10,'天梁'=>11,'七杀'=>12,'破军'=>13,
            '禄存'=>14,'羊刃'=>15,'陀罗'=>16,'天魁'=>17,'天钺'=>18,'红鸾'=>19,'天喜'=>20,
            '左辅'=>21,'右弼'=>22,'天刑'=>23,'天姚'=>24,'天马'=>25,
            '文昌'=>26,'文曲'=>27,'火'=>28,'铃'=>29
        ];
        $xc_no=$xc_no_data[$xc];
        $wd_data=[
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
        return $wd[$wd_data[$xc_no][$gw]];
    }
}
