<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace app\api\model\base;
/**
 * 文档基础模型
 */
class Base
{
    protected $use_new=0;
    /**
     * 真实出生时间
     * @param       string      $birthday       出生日期
     * @param       int         $sex            性别，默认为1（1男，0女）
     * @param       string      $city           城市
     * @param       int         $type           日期类型，默认为0（0公历，1农历）
     * @param       int         $is_jsreal      是否计算真实出生时间，默认为0（0否，1是）
     * @param       int         $is_23s         23时做第二天起始子时：1是，0否
     * @return      array
     * */
    public function get_all_birth_base($birthday,$sex=1,$city='',$type=0,$is_jsreal=0,$is_23s=1){
        if($type){
            $birth_watch=$this->nl_to_gl($birthday);
        }else{
            $birth_watch=['name'=>'出生地钟表时','value'=>date('Y-m-d H:i:01',strtotime($birthday))];
        }
        if($is_jsreal){
            $birth_real=$this->birth_real($birth_watch['value'],$city);
        }else{
            $birth_real['birth_real']=['name'=>'真实出生时间','value'=>$birth_watch['value']];
        }
        //出生日期相邻节气
        $birth_jq=$this->birth_jieqi($birth_real['birth_real']['value']);
        //农历出生日期
        $birth_nl=$this->birth_nongli($birth_real['birth_real']['value']);
        //八字
        $birth_bz=$this->birth_bz($birth_real['birth_real']['value'],$is_23s);
        //十天干十二地支生旺死绝
        $birth_swsj=$this->birth_swsj($birth_bz['value']);
        //五行
        $birth_wx=$this->birth_wx($birth_bz['value']);
        //生肖
        $birth_shengxiao=$this->birth_sx($birth_real['birth_real']['value']);
        //星宿
        $birth_xx=$this->birth_xx($birth_nl['birth_nl_no']['value']);
        //五常
        $birth_wc=$this->birth_wc($birth_shengxiao['value']['nl']);
        //臧干五行及五行强度
        $birth_zgwx=$this->birth_zgwx($birth_bz['value']);
        //能量磁场（宝石水晶）
        $birth_nlcc = $this->birth_nlcc($birth_zgwx['birth_wx_like']['value']);
        //本命佛
        $birth_bmf=$this->birth_bmf($birth_shengxiao['value']['yl']);
        //小儿关煞
        $birth_guansha=$this->birth_guansha($birth_bz['value']);
        //大运
        $birth_dayun=$this->birth_dayun($sex,$birth_real['birth_real']['value'],$birth_bz['value'],$birth_jq);
        //十神及简称
        $birth_shishen=$this->birth_shishen($birth_bz['value'],$birth_zgwx['birth_zgwx']['value']);
        //吉神凶煞
//        $birth_jishenxiongsha=$this->jishenxiongsha($birth_bz['value'],$sex);
        //星座属性
        $birth_star=$this->birth_star($birth_real['birth_real']['value']);
        //命卦
        $birth_minggua=$this->birth_minggua(date('Y',strtotime($birth_real['birth_real']['value'])),$sex);
        //日干支层次分析
        $birth_daygan=$this->birth_daygan($birth_bz['value']['day']['tgdz'],$birth_bz['value']['day']['tg']);
        //算春夏秋冬
        $birth_siji=$this->birth_siji(date('m',strtotime($birth_real['birth_real']['value'])));
        //命宫
        $birth_minggong=$this->birth_minggong($birth_bz['value']);
        //胎元
        $birth_taiyuan=$this->birth_taiyuan($birth_bz['value']);
        //四柱空亡
        $birth_kongwang=$this->birth_kongwang($birth_bz['value']);
        //后天胎息
//        $birth_taixi=$this->birth_taixi($birth_bz['value']);
        return [
            'birth_watch'=>$birth_watch,//出生地钟表时
            'birth_long'=>isset($birth_real['birth_long'])?$birth_real['birth_long']:[
                'name'=>'出生地经度',
                'value'=>''
            ],
            'birth_lat'=>isset($birth_real['birth_lat'])?$birth_real['birth_lat']:[
                'name'=>'出生地纬度',
                'value'=>''
            ],
            'pc_s'=>isset($birth_real['pc_s'])?$birth_real['pc_s']:[
                'name'=>'出生地偏差时间',
                'value'=>''
            ],
            'sun_pc_s'=>[
                'name'=>'真太阳偏差时间',
                'value'=>(isset($birth_real['sun_pc_s'])?$birth_real['sun_pc_s']:'')
            ],
            'birth_real'=>$birth_real['birth_real'],//真实出生时间
            'jieqi_1'=>$birth_jq['jieqi_1'],//相邻节气-前
            'jieqi_2'=>$birth_jq['jieqi_2'],//相邻节气-后
            'birth_nl'=>$birth_nl['birth_nl'],//农历出生时间
            'birth_nl_no'=>$birth_nl['birth_nl_no'],//农历出生时间，格式Y-m-d H:i:s
            'birth_nl_is_ry'=>$birth_nl['birth_nl_is_ry'],//是否是闰月
            'birth_bz'=>$birth_bz,//八字信息
            'birth_swsj'=>$birth_swsj,//生旺死绝
            'birth_wx'=>$birth_wx['birth_wx'],//八字五行
            'birth_wx_js'=>$birth_wx['birth_wx_js'],//八字五行-计数
	        'birth_wx_js_no'=>$birth_wx['birth_wx_js_no'],//八字五行-计数-数字下标
            'birth_wx_js_tgzg'=>[
                'name'=>'天干臧干五行合计',
                'value'=>[
                    '金'=>($birth_wx['birth_tg_js']['value']['金']['value']+$birth_zgwx['birth_zgwx_js']['value']['金']['value']),
                    '木'=>($birth_wx['birth_tg_js']['value']['木']['value']+$birth_zgwx['birth_zgwx_js']['value']['木']['value']),
                    '水'=>($birth_wx['birth_tg_js']['value']['水']['value']+$birth_zgwx['birth_zgwx_js']['value']['水']['value']),
                    '火'=>($birth_wx['birth_tg_js']['value']['火']['value']+$birth_zgwx['birth_zgwx_js']['value']['火']['value']),
                    '土'=>($birth_wx['birth_tg_js']['value']['土']['value']+$birth_zgwx['birth_zgwx_js']['value']['土']['value']),
                    'zj'=>($birth_wx['birth_tg_js']['value']['金']['value']+$birth_wx['birth_tg_js']['value']['木']['value']+$birth_wx['birth_tg_js']['value']['水']['value']+$birth_wx['birth_tg_js']['value']['火']['value']+$birth_wx['birth_tg_js']['value']['土']['value'])+$birth_zgwx['birth_zgwx_js']['value']['all']['value']
                ]
            ],
	        'birth_wx_js_tgzg_no'=>[
		        'name'=>'天干臧干五行合计-数字下标',
		        'value'=>[
			        $this->wx_col['金']=>($birth_wx['birth_tg_js']['value']['金']['value']+$birth_zgwx['birth_zgwx_js']['value']['金']['value']),
			        $this->wx_col['木']=>($birth_wx['birth_tg_js']['value']['木']['value']+$birth_zgwx['birth_zgwx_js']['value']['木']['value']),
			        $this->wx_col['土']=>($birth_wx['birth_tg_js']['value']['土']['value']+$birth_zgwx['birth_zgwx_js']['value']['土']['value']),
			        $this->wx_col['水']=>($birth_wx['birth_tg_js']['value']['水']['value']+$birth_zgwx['birth_zgwx_js']['value']['水']['value']),
			        $this->wx_col['火']=>($birth_wx['birth_tg_js']['value']['火']['value']+$birth_zgwx['birth_zgwx_js']['value']['火']['value']),
			        'zj'=>($birth_wx['birth_tg_js']['value']['金']['value']+$birth_wx['birth_tg_js']['value']['木']['value']+$birth_wx['birth_tg_js']['value']['水']['value']+$birth_wx['birth_tg_js']['value']['火']['value']+$birth_wx['birth_tg_js']['value']['土']['value'])+$birth_zgwx['birth_zgwx_js']['value']['all']['value']
		        ]
	        ],
            'birth_wxqq'=>$birth_wx['birth_wxqq'],//五行强度
            'birth_nywx'=>$birth_wx['birth_nywx'],//纳音五行
            'birth_nywx_year'=>$birth_wx['birth_nywx_year'],//纳音五行-年柱
            'birth_shengxiao'=>$birth_shengxiao,//生肖
            'birth_xx'=>$birth_xx,//星宿
            'birth_wc'=>$birth_wc,//五常
            'birth_zgwx'=>$birth_zgwx['birth_zgwx'],//臧干五行
            'birth_zgwx_js'=>$birth_zgwx['birth_zgwx_js'],//臧干五行-计数
	        'birth_zgwx_js_no'=>$birth_zgwx['birth_zgwx_js_no'],//臧干五行-计数-数字下标
            'birth_wxqd'=>$birth_zgwx['birth_wxqd'],//五行强度
            'birth_wxqd_hz'=>$birth_zgwx['birth_wxqd_hz'],//五行强度-汇总
	        'birth_wxqd_hz_no'=>$birth_zgwx['birth_wxqd_hz_no'],//五行强度-汇总-数字下标
            'birth_wxty'=>$birth_zgwx['birth_wxty'],//五行同异
            'birth_wxty_qd'=>$birth_zgwx['birth_wxty_qd'],//五行同异强度
            'birth_wx_rzqr'=>$birth_zgwx['birth_wx_rzqr'],//日主强弱
            'birth_wx_like'=>$birth_zgwx['birth_wx_like'],//五行喜用
            'birth_wx_bad'=>$birth_zgwx['birth_wx_bad'],//五行忌用
            'birth_nlcc'=>$birth_nlcc,//能量磁场
            'birth_bmf'=>$birth_bmf,//本命佛
            'birth_guansha'=>$birth_guansha['birth_guansha'],//小儿关煞
            'birth_guanshapj'=>$birth_guansha['birth_guanshapj'],//小儿关煞-破解
	        'birth_guanshapj_no'=>$birth_guansha['birth_guanshapj_no'],//小儿关煞-破解-数字下标
            'birth_lndy'=>$birth_dayun,//流年大运
            'birth_shishen'=>$birth_shishen['birth_shishen'],//十神
            'birth_shishen_zg'=>$birth_shishen['birth_shishen_zg'],//十神-臧干
            'birth_shishen_simple'=>$birth_shishen['birth_shishen_simple'],//十神-简称
            'birth_shishen_simple_zg'=>$birth_shishen['birth_shishen_zg_simple'],//十神-简称-臧干
//            'birth_jishenxiongsha'=>$birth_jishenxiongsha,
            'birth_star'=>$birth_star,//星座
            'birth_minggua'=>$birth_minggua,//命卦
            'birth_daygan'=>$birth_daygan,//日干支层次分析
            'birth_siji'=>$birth_siji,//算春夏秋冬
            'birth_minggong'=>$birth_minggong,//命宫
            'birth_kongwang'=>$birth_kongwang,//空亡
            'birth_taiyuan'=>$birth_taiyuan,//胎元
//            'birth_taixi'=>$birth_taixi,

        ];
    }
    /**
     * 农历转公历
     * @param       string      $birthday           出生日期
     * @param       int         $is_ry              是否闰月
     * @return      array
     */
    public function nl_to_gl($birthday,$is_ry=0){
    	//拆分年月日
    	$birth=explode(' ',$birthday);
    	$birth_date=explode('-',$birth[0]);
    	$year=$birth_date[0];
	    $month=$birth_date[1]+0;
	    $day=$birth_date[2]+0;
	    //调取时间插件模型
        $lunar = model('base.Lunar');
	    //获取闰月重新赋值月
        $rm=$lunar->getLeapMonth($year);
        if($month==$rm+0 && $is_ry){
            $month=$rm+1;
        }elseif($month>$rm && $rm){
            $month=$month+1;
        }
        //调取插件获取公历日期信息
        $aa = $lunar->convertLunarToSolar($year,$month,$day); //农历转公历
        $year=$aa[0];
        $month=$aa[1];
        $day=$aa[2];
        return ['name'=>'出生地钟表时','value'=>date('Y-m-d H:i:01',strtotime($year.'-'.$month.'-'.$day.' '.$birth[1]))];;
    }
    /*
     * 真实出生时间
     * @param   string      $birth      公历出生日期
     * @param   string      $city       出生地（市）
     * @return  array
     * */
    public function birth_real($birth,$city){

        /**计算出生地偏差时间**/
        //获取所有出生地偏差数据-缓存
        $city_pc_time_arr=model('db_base.BaseDb')->base_positiontbl();
        //取出出生地$city的偏差数据
        $city_pc_time=$city_pc_time_arr[$city];
        //出生地偏差时间
        if($city){
	        //偏差时间转化成秒单位
            $pc_s=$this->h_s($city_pc_time['p_poor_time'],$city_pc_time['p_calc_type']);
        }else{
	        $pc_s=0;
        }
	    //实际出生时间
	    $real_city_birth=strtotime($birth)+$pc_s;

        $month=date('m',strtotime($birth))+0;
        $day=date('d',strtotime($birth));
        //真太阳偏差时间
        $sun_pc_arr=model('db_base.BaseDb')->base_suntimetbl();
        $sun_pc=$sun_pc_arr[$month.'月'.$day.'日'];
        //偏差时间转化成秒单位
        $sun_pc_s=$this->h_s($sun_pc['s_calc_time'],$sun_pc['s_calc_type']);
        //计算实际出生时间
        $real_birth=date('Y-m-d H:i:s',$real_city_birth+$sun_pc_s);
        //组装偏差时及真实出生时间输出数组
        $data=array(
            'birth_long'=>[
                'name'=>'出生地经度',
                'value'=>($city_pc_time?$city_pc_time['p_longitude']:'')
            ],
            'birth_lat'=>[
                'name'=>'出生地纬度',
                'value'=>($city_pc_time?$city_pc_time['p_latitude']:'')
            ],
            'pc_s'=>[
                'name'=>'出生地偏差时间',
                'value'=>$pc_s
            ],
            'sun_pc_s'=>[
                'name'=>'真太阳偏差时间',
                'value'=>$sun_pc_s
            ],
            'birth_real'=>[
                'name'=>'真实出生时间',
                'value'=>$real_birth
            ]
        );
        return $data;

    }
    /**
     * @function        出生相邻节气
     * @param   string  $date     真实出生时间
     * @return  array
     */
    public function birth_jieqi($date){
        //读取节气数据缓存
        $jieqi_arr=model('db_base.BaseDb')->base_solartermstbl();
        /**计算节气数据**/
        //出生年
        $date_year=explode('-',$date)[0];
        //出生日期时间戳
        $date_time=strtotime($date);
        //设置查询年范围-缩小查询范围
        //查询节气起始年-（出生年-1）
        $jieqi_year_s=$date_year-1;
        //查询节气结束年-（出生年+1）
        $jieqi_year_e=$date_year+1;
        //初始化前、后节气时间初始值
        $date_time_1=strtotime('1902-01-01');//前节气初始时间
        $date_time_2=strtotime(($jieqi_year_e+1).'-01-01');//后节气初始时间
        //冒泡获取前、后节气
        for($y=$jieqi_year_s;$y<=$jieqi_year_e;$y++){
            foreach ($jieqi_arr[$y] as $v){
                //节气时间戳
                $jieqi_time=strtotime($v['st_time']);
                //前节气时间冒泡获取
                if($jieqi_time<=$date_time && $date_time_1<=$jieqi_time){
                    $date_time_1=$jieqi_time;
                    $jieqi['jieqi_1']=$v;
                }
                //后节气时间冒泡获取
                if($jieqi_time>$date_time && $date_time_2>=$jieqi_time){
                    $jieqi['jieqi_2']=$v;
                    $date_time_2=$jieqi_time;
                }
            }
        }
	    //组装偏差时及真实出生时间输出数组
        $data=[
            'jieqi_1'=>[
                'name'=>'相邻节气(前)',
                'value'=>$jieqi['jieqi_1']
            ],
            'jieqi_2'=>[
                'name'=>'相邻节气(后)',
                'value'=>$jieqi['jieqi_2']
            ]
        ];
        return $data;
    }
    /**
     * 农历出生日期
     * @param   string  $date     真实出生时间
     * @return  array
     * */
    public function birth_nongli($date){
        //取出出生时间的年、月、日
        $date_arr=explode(' ',$date);
        $date_birth=explode('-',$date_arr[0]);
        $year=$date_birth[0];
        $month=$date_birth[1];
        $day=$date_birth[2];
        //调用转换插件获取农历信息
	    //调取时间插件模型
        $lunar = model('base.Lunar');
        //调取公历转农历函数
        $data_nongli=$lunar->convertSolarToLunar($year,$month,$day);
        //处理农历信息
        $td_year=$data_nongli[3];
        $year_no=$data_nongli[0];
        $td_month=$data_nongli[1];
        //闰月判断处理
        if($data_nongli[7]){
            $nongli['is_ry']=($data_nongli[7]==$td_month)?1:0;//判断闰月
	        //闰月及之后的月份数字-1
            $td_month_no=($data_nongli[4]+0)>$data_nongli[7]?($data_nongli[4]-1):$data_nongli[4];
        }else{
            $td_month_no=$data_nongli[4];
        }
        $td_day=$data_nongli[2];
        $td_day_no=$data_nongli[5];
        //出生时辰转化时支
        $date_hour=explode(' ',$date);
        $dz_hour=$this->dz_arr;
        $date_h=explode(':',$date_hour[1]);
        //整理输出数组
        $nongli['nongli']=$td_year.'年 '.$td_month.' '.$td_day.' '.$dz_hour[ceil($date_h[0]/2)].'时';
        $nongli['nongli_no']=$year_no.'-'.($td_month_no>9?'':'0').$td_month_no.'-'.($td_day_no>9?'':'0').$td_day_no.' '.$date_hour[1];
        $nongli['is_ry']=mb_strstr($td_month,'闰','utf-8')!==false?1:0;
        $data=[
            'birth_nl'=>[
                'name'=>'农历出生日期',
                'value'=>$nongli['nongli']
            ],
            'birth_nl_no'=>[
                'name'=>'农历出生日期（数字）',
                'value'=>$nongli['nongli_no']
            ],
            'birth_nl_is_ry'=>[
                'name'=>'农历月份是否为闰月',
                'value'=>$nongli['is_ry']
            ]
        ];
        return $data;

    }
    /**
     * 八字
     * @param   string  $real_date    真实出生时间
     * @return  array
     * */
    public function birth_bz($real_date,$is_23s=1){
        //获取出生时间的节气数据
        $jieqi=$this->birth_jieqi($real_date);
        //取出前节气数据
        $month=$jieqi['jieqi_1']['value'];
        //取出前节气数据的年
        $year=$month['st_year'];
        //年天干
        $year_tg=$this->tg_arr[($year-4)%10];
        //年地支
        $year_dz=$this->dz_arr[($year-4)%12];
        /**计算月干支**/
        //月地支
        $month_dz=mb_substr($month['st_dzname'],0,1,'utf-8');
        //月干：月干下标=（(月支数字+9)%12+（年干数字）*2）%10
        $month_tg=$this->tg_arr[(($this->dz_num[$month_dz]+9)%12+($this->tg_num[$year_tg])*2)%10];
        /*计算日天干地支*/
        //日天干求解参数
	    //拆分日期
        $real_date_arr=explode('-',$real_date);
        //获取年
        $year=$real_date_arr[0];
        //年前两位数字
        $year_12=substr($year,0,2);
        //年后两位数字
        $year_34=substr($year,2,2);
        $real_month=$real_date_arr[1];
        //1月、2月特殊处理
        if($real_month=='01'|| $real_month=='02'){
            $real_month=$real_month=='01'?13:14;
            $year_34-=1;
        }
        $real_day=explode(' ',$real_date_arr[2])[0];
	    //日天干求解参数
        $g=4*$year_12+floor($year_12/4)+5*$year_34+floor($year_34/4)+floor(3*($real_month+1)/5)+$real_day-3;
        //日地支求解参数
        $z=8*$year_12+floor($year_12/4)+5*$year_34+floor($year_34/4)+floor(3*($real_month+1)/5)+$real_day+7+($real_month%2?0:6);
        //23时之后为第二天时参数+1
        if($is_23s && date('H',strtotime($real_date))==23){
            $g+=1;
            $z+=1;
        }
        /**获取日干支**/
        //日干
        $day_tg=$this->tg_arr[($g-1)%10];
        //日支
        $day_dz=$this->dz_arr[($z-1)%12];
        /**获取时干支**/
        //获取时干支查询列表
        $hour=date('H',strtotime($real_date))+0;
        $dz_index=floor(($hour+1)%24/2);
        //时天干
        $hour_tg=$this->tg_arr[($dz_index%10+($this->tg_num[$day_tg]-1)*2)%10];
        //时地支
        $hour_dz=$this->dz_arr[$dz_index%10];
        //获取出生时干支数据
        $hour_name=['夜半','鸡鸣','平旦','日出','食时','隅中','日中','日跌','晡食','日入','黄昏','人定'];
        $data=[
            'year'=>[
                'tgdz'=>$year_tg.$year_dz,
                'tg'=>$year_tg,
                'dz'=>$year_dz
            ],
            'month'=>[
                'tgdz'=>$month_tg.$month_dz,
                'tg'=>$month_tg,
                'dz'=>$month_dz
            ],
            'day'=>[
                'tgdz'=>$day_tg.$day_dz,
                'tg'=>$day_tg,
                'dz'=>$day_dz
            ],
            'hour'=>[
                'tgdz'=>$hour_tg.$hour_dz,
                'tg'=>$hour_tg,
                'dz'=>$hour_dz
            ],
            'sucheng'=>$hour_name[$dz_index]
        ];
        return ['name'=>'生辰八字','value'=>$data];
    }
    /**
     * 十天干十二地支生旺死绝
     * @param   array       $birth_bz       八字
     * @return  array       $data
     */
    public function birth_swsj($birth_bz){
    	//获取生旺死绝数据表
        $swsj_db=model('db_base.BaseDb')->base_swsj();
        //取出年月日时四柱的生旺死绝
        $year_data=$swsj_db[$birth_bz['year']['dz']];
        $month_data=$swsj_db[$birth_bz['month']['dz']];
        $day_data=$swsj_db[$birth_bz['day']['dz']];
        $hour_data=$swsj_db[$birth_bz['hour']['dz']];
        $data=[
            'name'=>'十天干十二地支生旺死绝',
            'value'=>[
                'year'=>$year_data['tg'.$this->tg_num[$birth_bz['day']['tg']]],
                'month'=>$month_data['tg'.$this->tg_num[$birth_bz['day']['tg']]],
                'day'=>$day_data['tg'.$this->tg_num[$birth_bz['day']['tg']]],
                'hour'=>$hour_data['tg'.$this->tg_num[$birth_bz['day']['tg']]],
            ]
        ];
        return $data;
    }
    /**
     * 五行
     * @param   array   $bz           八字：$this->birth_bz($date,$real_date)
     * @return  array
     * */
    public function birth_wx($bz){
        //干支五行
        $gzwuxing_db=model('db_base.BaseDb')->base_gzwuxing();
        $data_wx=[
            'year'=>[
                'tgdz'=>$gzwuxing_db[$bz['year']['tg']].$gzwuxing_db[$bz['year']['dz']],
                'tg'=>$gzwuxing_db[$bz['year']['tg']],
                'dz'=>$gzwuxing_db[$bz['year']['dz']]
            ],
            'month'=>[
                'tgdz'=>$gzwuxing_db[$bz['month']['tg']].$gzwuxing_db[$bz['month']['dz']],
                'tg'=>$gzwuxing_db[$bz['month']['tg']],
                'dz'=>$gzwuxing_db[$bz['month']['dz']]
            ],
            'day'=>[
                'tgdz'=>$gzwuxing_db[$bz['day']['tg']].$gzwuxing_db[$bz['day']['dz']],
                'tg'=>$gzwuxing_db[$bz['day']['tg']],
                'dz'=>$gzwuxing_db[$bz['day']['dz']]
            ],
            'hour'=>[
                'tgdz'=>$gzwuxing_db[$bz['hour']['tg']].$gzwuxing_db[$bz['hour']['dz']],
                'tg'=>$gzwuxing_db[$bz['hour']['tg']],
                'dz'=>$gzwuxing_db[$bz['hour']['dz']]
            ],
        ];
        //五行计数
        $wx_js=[
            '金'=>[
                'name'=>'金',
                'value'=>0
            ],
            '木'=>[
                'name'=>'木',
                'value'=>0
            ],
            '水'=>[
                'name'=>'水',
                'value'=>0
            ],
            '火'=>[
                'name'=>'火',
                'value'=>0
            ],
            '土'=>[
                'name'=>'土',
                'value'=>0
            ],
        ];
        $wx_js_no=[];
        $wx_tgjs=[
            '金'=>[
                'name'=>'金',
                'value'=>0
            ],
            '木'=>[
                'name'=>'木',
                'value'=>0
            ],
            '水'=>[
                'name'=>'水',
                'value'=>0
            ],
            '火'=>[
                'name'=>'火',
                'value'=>0
            ],
            '土'=>[
                'name'=>'土',
                'value'=>0
            ],
        ];
	    $wx_tgjs_no=[];
        foreach ($data_wx as $v){
            $wx_js[$v['tg']]['value']+=1;
            $wx_js[$v['dz']]['value']+=1;
            $wx_tgjs[$v['tg']]['value']+=1;
        }
        //五行计数-数字下标版
	    foreach ($wx_js as $k=>$v){
        	$wx_js_no[$this->wx_col[$k]]=$v;
	    }
	    //天干计数-数字下标版
	    foreach ($wx_tgjs as $k=>$v){
		    $wx_tgjs_no[$this->wx_col[$k]]=$v;
	    }
        //纳音五行
        $nywuxing_db=model('db_base.BaseDb')->base_nywuxing();
        $data_ny=[
            'year'=>$nywuxing_db[$bz['year']['tgdz']],
            'month'=>$nywuxing_db[$bz['month']['tgdz']],
            'day'=>$nywuxing_db[$bz['day']['tgdz']],
            'hour'=>$nywuxing_db[$bz['hour']['tgdz']]
        ];
        //五行齐缺
        $wxqq=[];
        foreach ($wx_js as $v){
            if(!$v['value']){
                $wxqq[]=$v['name'];
            }
        }
        $birth_wxqq='五行'.($wxqq?('缺'.implode('、',$wxqq)):'齐全');
        $data=[
            'birth_wx'=>[
                'name'=>'干支五行',
                'value'=>$data_wx
            ],
            'birth_wx_js'=>[
                'name'=>'五行计数',
                'value'=>$wx_js
            ],
	        'birth_wx_js_no'=>[
		        'name'=>'五行计数-数字下标',
		        'value'=>$wx_js_no
	        ],
            'birth_nywx'=>[
                'name'=>'纳音五行',
                'value'=>$data_ny
            ],
            'birth_nywx_year'=>[
                'name'=>'年柱纳音五行',
                'value'=>mb_substr($data_ny['year'],2,1,'utf-8')
            ],
            'birth_wxqq'=>[
                'name'=>'五行齐缺',
                'value'=>$birth_wxqq
            ],
            'birth_tg_js'=>[
            	'name'=>'天干计数',
	            'value'=>$wx_tgjs
            ],
	        'birth_tg_js_no'=>[
		        'name'=>'天干计数-数字下标',
		        'value'=>$wx_tgjs_no
	        ]
        ];
        return $data;
    }
    /**
     * 生肖
     * @param   string      $date      真实出生日期
     * @return  array
    */
    public function birth_sx($date){
        /**出生公历年**/
        $year_gl=explode('-',$date)[0];
        /**出生农历年**/
        $nongli=$this->birth_nongli($date);
        $year_nl=explode('-',$nongli['birth_nl_no']['value'])[0];
        /**出生月历年**/
        //获取出生时间的节气数据
        $jieqi_1=$this->birth_jieqi($date)['jieqi_1']['value'];
        //月历年
        $year_yl=$jieqi_1['st_year'];
        $sxjj=['鼠'=>4,'牛'=>4,'虎'=>6,'兔'=>4,'龙'=>7,'蛇'=>3,'马'=>3,'羊'=>5,'猴'=>4,'鸡'=>4,'狗'=>4,'猪'=>6];
        $data=[
            'name'=>'生肖',
            'value'=>[
                'gl'=>$this->sx_arr[($year_gl+8)%12],
                'gl_img'=>($year_gl+8)%12+1,
                'nl'=>$this->sx_arr[($year_nl+8)%12],
                'nl_img'=>($year_nl+8)%12+1,
                'yl'=>$this->sx_arr[($year_yl+8)%12],
                'yl_img'=>($year_yl+8)%12+1,
                'sxjj'=>$sxjj[$this->sx_arr[($year_yl+8)%12]]
            ]
        ];
        return $data;
    }
    /**
     *星宿
     * @param   string   $date         农历出生日期
     * @return  array
     **/
    public function birth_xx($date){
    	//拆分出生日期-农历日期不能用时间转换函数转换
        $date=explode(' ',$date);
        $date=explode('-',$date[0]);
        $m=$date[1]+0;
        $d=$date[2]+0;
        //获取月日星宿对应表
        $xx_arr=model('db_base.BaseDb')->base_mdxingxiu();
        //得出星宿
        $xx=$xx_arr[$d]['mon'.$m];
        //获取星宿所属四象对应表
        $xingxiutype_db=model('db_base.BaseDb')->base_xingxiutype();
        //得出四象
        $sx=$xingxiutype_db[$xx];
        $data=[
            'name'=>'星宿四象',
            'value'=> [
                'xx'=>$xx,
                'sx'=>$sx['sx_name'],
                'fw'=>$sx['fw']
            ]
        ];
        return $data;

    }
    /**
     * 五常
     * @param   string  $shengxiao      农历生肖
     * @return  array
    */
    public function birth_wc($shengxiao){
        $wuchang_db=model('db_base.BaseDb')->base_wuchang();
        $data=[
            'name'=>'生肖五常',
            'value'=>$wuchang_db[$shengxiao]
        ];
        return $data;
    }
    /**
     * 臧干五行及五行强度
     * @param   array   $bz     八字：birth_bz()
     * @return  array
    */
    public function birth_zgwx($bz){
    	//获取四柱臧干
        $zanggan_db=model('db_base.BaseDb')->base_zanggan();
        $zgwx=[
            'year'=>$zanggan_db[$bz['year']['dz']],
            'month'=>$zanggan_db[$bz['month']['dz']],
            'day'=>$zanggan_db[$bz['day']['dz']],
            'hour'=>$zanggan_db[$bz['hour']['dz']]
        ];
        //臧干五行-计数
        $zgwx_js=['金'=>0,'木'=>0,'水'=>0,'火'=>0,'土'=>0,'all'=>0];
        foreach ($zgwx as $val){
            foreach (explode('、',$val['wx']) as $v){
                $zgwx_js[$v]+=1;
                $zgwx_js['all']+=1;
            }
        }
        //组装月支字段
        $mdz='dz'.$this->dz_num[$bz['month']['dz']];
        //四柱天干五行强度
        $tg_wuxingqd_db=model('db_base.BaseDb')->base_wuxingqd_tg($this->use_new);
        //年天干五行强度
        $wxqd_y=$tg_wuxingqd_db[$bz['year']['tg']];
        //月天干五行强度
        $wxqd_m=$tg_wuxingqd_db[$bz['month']['tg']];
        //日天干五行强度
        $wxqd_d=$tg_wuxingqd_db[$bz['day']['tg']];
        //时天干五行强度
        $wxqd_h=$tg_wuxingqd_db[$bz['hour']['tg']];
        //臧干五行强度
        //年臧干五行强度
        $wxqd_yzg=$this->zgwxqd_slt($zgwx['year'],$mdz);
        //月臧干五行强度
        $wxqd_mzg=$this->zgwxqd_slt($zgwx['month'],$mdz);
        //日臧干五行强度
        $wxqd_dzg=$this->zgwxqd_slt($zgwx['day'],$mdz);
        //时臧干五行强度
        $wxqd_hzg=$this->zgwxqd_slt($zgwx['hour'],$mdz);
        $wxqd=[
            'year'=>['name'=>$wxqd_y['wx'],'value'=>round($wxqd_y['dz'.$this->dz_num[$bz['month']['dz']]],3)],
            'month'=>['name'=>$wxqd_m['wx'],'value'=>round($wxqd_m['dz'.$this->dz_num[$bz['month']['dz']]],3)],
            'day'=>['name'=>$wxqd_d['wx'],'value'=>round($wxqd_d['dz'.$this->dz_num[$bz['month']['dz']]],3)],
            'hour'=>['name'=>$wxqd_h['wx'],'value'=>round($wxqd_h['dz'.$this->dz_num[$bz['month']['dz']]],3)],
            'zg_year'=>$wxqd_yzg,
            'zg_month'=>$wxqd_mzg,
            'zg_day'=>$wxqd_dzg,
            'zg_hour'=>$wxqd_hzg,
        ];
        //五行强度-汇总计算
        $wxqd_all=['金'=>0,'木'=>0,'水'=>0,'火'=>0,'土'=>0,];
        foreach ($wxqd as $val){
            if(isset($val['name'])){
                $wxqd_all[$val['name']]+=$val['value'];
            }else{
                foreach ($val as $v){
                    $wxqd_all[$v['name']]+=$v['value'];
                }
            }
        }
        //五行强度汇总-数字下标
	    $wxqd_all_no=[];
	    foreach ($wxqd_all as $k=>$v){
		    $wxqd_all_no[$this->wx_col[$k]]=$v;
	    }
        //日主五行同类异类
        $wx_ty_arr=[
            '金'=>[
                'tl'=>['土','金'],
                'yl'=>['水','木','火']
            ],
            '木'=>[
                'tl'=>['水','木'],
                'yl'=>['火','土','金']
            ],
            '土'=>[
                'tl'=>['火','土'],
                'yl'=>['金','水','木']
            ],
            '水'=>[
                'tl'=>['金','水'],
                'yl'=>['木','火','土']
            ],
            '火'=>[
                'tl'=>['木','火'],
                'yl'=>['土','金','水']
            ]
        ];
        $wxty=$wx_ty_arr[$wxqd_d['wx']];
        $tlqd=$ylqd=0;
        foreach ($wxty['tl'] as $v) {
            //五行同类总强度
            $tlqd += $wxqd_all[$v];
        }
        foreach ($wxty['yl'] as $v) {
            //五行异类总强度
            $ylqd += $wxqd_all[$v];
        }
        //五行喜用
        $wx_like = $tlqd > $ylqd ? $wxty['yl'] : $wxty['tl'];
        //五行忌用
        $wx_bad = $tlqd > $ylqd ? $wxty['tl'] : $wxty['yl'];
        $data=[
            'birth_zgwx'=>[
                'name'=>'臧干五行',
                'value'=>$zgwx
            ],
            'birth_zgwx_js'=>[
                'name'=>'臧干五行计数',
                'value'=>[
                    '金'=>[
                        'name'=>'金',
                        'value'=>$zgwx_js['金']
                    ],
                    '木'=>[
                        'name'=>'木',
                        'value'=>$zgwx_js['木']
                    ],
                    '水'=>[
                        'name'=>'水',
                        'value'=>$zgwx_js['水']
                    ],
                    '火'=>[
                        'name'=>'火',
                        'value'=>$zgwx_js['火']
                    ],
                    '土'=>[
                        'name'=>'土',
                        'value'=>$zgwx_js['土']
                    ],
                    'all'=>[
                        'name'=>'臧干五行总数',
                        'value'=>$zgwx_js['all']
                    ],
                ]
            ],
	        'birth_zgwx_js_no'=>[
		        'name'=>'臧干五行计数-数字下标',
		        'value'=>[
			        $this->wx_col['金']=>[
				        'name'=>'金',
				        'value'=>$zgwx_js['金']
			        ],
			        $this->wx_col['木']=>[
				        'name'=>'木',
				        'value'=>$zgwx_js['木']
			        ],
			        $this->wx_col['土']=>[
				        'name'=>'土',
				        'value'=>$zgwx_js['土']
			        ],
			        $this->wx_col['水']=>[
				        'name'=>'水',
				        'value'=>$zgwx_js['水']
			        ],
			        $this->wx_col['火']=>[
				        'name'=>'火',
				        'value'=>$zgwx_js['火']
			        ],
			        'all'=>[
				        'name'=>'臧干五行总数',
				        'value'=>$zgwx_js['all']
			        ],
		        ]
	        ],
            'birth_wxqd'=>[
                'name'=>'五行强度',
                'value'=>$wxqd
            ],
            'birth_wxqd_hz'=>[
                'name'=>'五行强度汇总',
                'value'=>$wxqd_all
            ],
	        'birth_wxqd_hz_no'=>[
		        'name'=>'五行强度汇总-数字下标',
		        'value'=>$wxqd_all_no
	        ],
            'birth_wxty'=>[
                'name'=>'五行同异',
                'value'=>$wxty
            ],
            'birth_wxty_qd'=>[
                'name'=>'同异类五行强度',
                'value'=>[
                    'tl'=>round($tlqd,3),
                    'yl'=>round($ylqd,3)
                ]
            ],
            'birth_wx_like'=>[
                'name'=>'五行喜用',
                'value'=>$wx_like
            ],
            'birth_wx_bad'=>[
                'name'=>'五行忌用',
                'value'=>$wx_bad
            ],
            'birth_wx_rzqr'=>[
                'name'=>'日主强弱',
                'value'=>($tlqd>=$ylqd?'强':'弱')
            ]
        ];
        return $data;

    }
    /**
     * 臧干五行强度查询
     * @param   array   $zg         臧干
     * @param   string  $mdz        月地支
     * @return  array
     * **/
    public function zgwxqd_slt($zg,$mdz){
        $arr_zg=explode('、',$zg['zg']);
        $zgwx_qd_arr=model('db_base.BaseDb')->base_wuxingqd_zg($this->use_new);
        $data=[];
        foreach ($arr_zg as $v){
            if(isset($zgwx_qd_arr[$zg['dz'].$v])){
                $arr=$zgwx_qd_arr[$zg['dz'].$v];
                $data[]=['name'=>$arr['wx'],'value'=>round($arr[$mdz],3)];
            }
        }
        return $data;
    }
    /**
     * 偏差时间转秒数
     * @param   string  $time         偏差时间（默认格式:H:i:s）
     * @param   string  $type         偏差类型（加或减）
     * @param   string  $str          时间分割符（默认：":"）
     * @return  int
     * */
    public function h_s($time,$type,$str=':'){
        $pc_time=explode($str,$time);
        //出生地偏差时间
        $pc_s=$pc_time[0]*3600+$pc_time[1]*60+$pc_time[2];
        return $type=='加'?$pc_s:-$pc_s;
    }
    /**
     * 生肖守护神
     * @param   string      $sx     月历生肖
     * @return  array
     * */
    public function birth_bmf($sx){
        $sxshs=model('db_base.BaseDb')->base_sxshs();
        $data=[
            'name'=>'生肖守护神',
            'value'=>$sxshs[$sx]
        ];
        return $data;
    }
    /**
     * 能量磁场
     * @param   array       $wx_like    五行喜用
     * @return  array
     * **/
    public function birth_nlcc($wx_like){
        $nlcc=[
            '金'=>'白发晶',
            '木'=>'绿发晶',
            '水'=>'黑发晶',
            '火'=>'红发晶',
            '土'=>'钛晶',
        ];
        $nlcc_data=[];
        foreach ($wx_like as $v){
            $data[]=['wx'=>$v,'bs'=>$nlcc[$v]];
        }
        $data=[
            'name'=>'能量磁场',
            'value'=>$nlcc_data
        ];
        return $data;
    }

    /**
     * 姓氏数据
     * @param   string  $fname      姓
     * @return  array
     * @edit        2018-5-30       输出数据格式调整
     * @edit        2018-6-11       添加缓存时间             王龙起
     * @edit        2018-6-13       添加图腾图片输出         王龙起
     */
    public function fname_data($fname){
        $baijiaxing_db=model('db_base.BaseDb')->base_baijiaxing();
        $data=[];
        if(isset($baijiaxing_db[$fname])){
            $data['fname']=['name'=>'姓氏','value'=>$baijiaxing_db[$fname]['fname']];
            $data['fname_py']=['name'=>'拼音','value'=>$baijiaxing_db[$fname]['fname_py']];
            $data['fname_en']=['name'=>'外文名','value'=>$baijiaxing_db[$fname]['fname_en']];
            $data['fname_ancestor']=['name'=>'得姓始祖','value'=>$baijiaxing_db[$fname]['fname_ancestor']];
            $data['fname_celebrity']=['name'=>'历史名人','value'=>$baijiaxing_db[$fname]['fname_celebrity']];
            $data['fname_origin']=['name'=>'姓氏起源','value'=>$baijiaxing_db[$fname]['fname_origin']];
            $data['fname_migrate']=['name'=>'迁徙路线','value'=>$baijiaxing_db[$fname]['fname_migrate']];
            $data['fname_distribution']=['name'=>'人口分布','value'=>$baijiaxing_db[$fname]['fname_distribution']];
            $data['fname_totem_img']=['name'=>'图腾图片编号','value'=>$baijiaxing_db[$fname]['fname_totem_img']];
            $data['fname_totem']=['name'=>'图腾','value'=>$baijiaxing_db[$fname]['fname_totem']];
            $data['fname_py_sm']=['name'=>'声母','value'=>$baijiaxing_db[$fname]['fname_py_sm']];
            $data['fname_py_ym']=['name'=>'韵母','value'=>$baijiaxing_db[$fname]['fname_py_ym']];
            $data['fname_py_wd']=['name'=>'拼音无调','value'=>$baijiaxing_db[$fname]['fname_py_wd']];
            $data['fname_sd']=['name'=>'声调','value'=>$baijiaxing_db[$fname]['fname_sd']];
            $data['fname_totem_img_new']=['name'=>'图腾图片（新）','value'=>($baijiaxing_db[$fname]['fname_totem_img_new']?json_decode($baijiaxing_db[$fname]['fname_totem_img_new'],true):'')];
        }
        return $data;
    }
    /**
     * 小儿关煞
     * @param   array   $bz         八字
     * @return  array   $data       小儿关煞数组
     */
    public function birth_guansha($bz){
        $year=$bz['year']['dz'];
        //获取月支的月份数字：寅为1月，卯为2月······
        $month=($this->dz_num[$bz['month']['dz']]+9)%12+1;
        $day=$bz['day']['tg'];
        //组装日支字段
        $hour='dz'.$this->dz_num[$bz['hour']['dz']];
        //查询小儿关煞对照表
        $guansha_db=model('db_base.BaseDb')->base_guansha();//数据表区分月与时的获取，月份存月份数字，时存时支
        $year_data=$guansha_db[$year];
        $month_data=$guansha_db[$month];
        $day_data=$guansha_db[$day];
        $guansha_data['year']=$year_data[$hour];
        $guansha_data['month']=$month_data[$hour];
        $guansha_data['day']=$day_data[$hour];
        //获取小儿关煞破解
        $gsxj=model('db_base.BaseDb')->base_guanshapj();
        $guansha=[];//小儿关煞破解
	    $guansha_no=[];//小儿关煞破解-数字下标
        foreach ($guansha_data as $val){
            $arr=explode('、',$val);
            if($arr){
                foreach ($arr as $v){
                    if($v){
                        $guansha[$v]=$gsxj[$v];
	                    $guansha_no[]=$gsxj[$v];
                    }
                }
            }
        }
        $data=[
            'birth_guansha'=>[
                'name'=>'小儿关煞',
                'value'=>['year'=>$year_data[$hour],'month'=>$month_data[$hour],'day'=>$day_data[$hour]]
            ],
            'birth_guanshapj'=>[
                'name'=>'小儿关煞破解',
                'value'=>$guansha
            ],
	        'birth_guanshapj_no'=>[
		        'name'=>'小儿关煞破解-数字下标',
		        'value'=>$guansha_no
	        ]
        ];
        return $data;
    }
    /**
     * 流年大运
     * @param   int             $sex            性别（1男，0女）
     * @param   string          $birth_real     真实出生日期
     * @param   array           $bz             八字
     * @param   array           $birth_jq       相邻节气
     * @return  array           $data
     */
    public function birth_dayun($sex,$birth_real,$bz,$birth_jq){
        //节点天干数字
        $jd_tg=$this->tg_num[$bz['month']['tg']]-1;
        //节点地支数字
        $jd_dz=$this->dz_num[$bz['month']['dz']]-1;
        //年干阴阳：阳干->甲丙戊庚壬，阴干->乙丁己辛癸
        $dy_ytgyy=$this->tg_num[$bz['year']['tg']]%2?1:-1;
        //性别阴阳：男阳女阴
        $sex_yy=$sex?1:-1;
        //大运流年获取顺序-正序（1）|倒序（-1）
        $dy_lnpx=$sex_yy*$dy_ytgyy;
        //大运流年干支
        $dy_tgdz=[];
        for($i=1;$i<11;$i++){
            $dy_tgdz[]=$this->tg_arr[($jd_tg+$dy_lnpx*$i+10)%10].$this->dz_arr[($jd_dz+$dy_lnpx*$i+12)%12];
        }
        //大运流年节点干支
        $dy_jd=$this->tg_arr[$jd_tg].$this->dz_arr[$jd_dz];
        /**计算起运年龄**/
        //节气-前
        $jq_1=strtotime($birth_jq['jieqi_1']['value']['st_time']);
        //节气-后
        $jq_2=strtotime($birth_jq['jieqi_2']['value']['st_time']);
        //出生时间
        $realtime=strtotime($birth_real);
        //相差秒数
        $day_cs=($dy_lnpx<0?($realtime-$jq_1):($jq_2-$realtime));
        /**起运年龄-精确至年月日**/
        //起运年龄-岁
        $age_y=floor($day_cs/3/3600/24);
        //起运时间-月
        $age_m=floor($day_cs%(3*3600*24)/6/3600);
        //起运时间-日
        $age_d=floor($day_cs%(6*3600)/720);
        //精确至年
        $age_y=round($day_cs/3/3600/24);
        if($age_y==0){
            $age_y=10;
        }
        $dayun['dy_ytgyy']=$dy_ytgyy;//年干阴阳->1阳，-1阴
        $dayun['dy_sex_yy']=$sex_yy;//性别阴阳->1阳，-1阴
        $dayun['dy_lnpx']=$dy_lnpx;//流年大运顺序->1正序,-1倒序
        $dayun['dy_tgdz']=$dy_tgdz;//流年大运干支
        $dayun['dy_jd']=$dy_jd;//流年大运节点干支
        $dayun['dy_qy'] = ['day' => $age_d, 'month' => $age_m,'age'=>$age_y];//起运年龄
        $dayun['dy_age'] = [];//流年大运起运年龄
        $dayun['dy_year_s'] = [];//流年大运起运年
        $dayun['dy_year_e'] = [];//流年大运结束年
        for ($i=0;$i<10;$i++){
            $dayun['dy_age'][]=$age_y+$i*10;
            $dayun['dy_year_s'][]=date('Y',strtotime($birth_real))+$age_y+$i*10;
            $dayun['dy_year_e'][]=date('Y',strtotime($birth_real))+$age_y+$i*10+9;
        }
        $data=[
            'name'=>'流年大运',
            'value'=>$dayun
        ];
        return $data;

    }

    /**
     * 十神
     * @param   array           $birth_bz           八字
     * @param   string          $birth_zg           臧干
     * @return  array           $data
     * 修改：添加十神简称        2018-5-15       王龙起
     */
    public function birth_shishen($birth_bz,$birth_zg=''){
        $sh=[1=>'正财',2=>'偏财',3=>'正官',4=>'偏官',5=>'正印',6=>'偏印',7=>'劫财',8=>'比肩',9=>'伤官',10=>'食神'];
        $sh1=[1=>'才',2=>'财',3=>'官',4=>'杀',5=>'印',6=>'枭',7=>'劫',8=>'比',9=>'伤',10=>'食'];
	    //十神对照表
	    $sh_table=[
		    '甲'=>['甲'=>8,'乙'=>7,'丙'=>10,'丁'=>9,'戊'=>2,'己'=>1,'庚'=>4,'辛'=>3,'壬'=>6,'癸'=>5],
		    '乙'=>['甲'=>7,'乙'=>8,'丙'=>9,'丁'=>10,'戊'=>1,'己'=>2,'庚'=>3,'辛'=>4,'壬'=>5,'癸'=>6],
		    '丙'=>['甲'=>6,'乙'=>5,'丙'=>8,'丁'=>7,'戊'=>10,'己'=>9,'庚'=>2,'辛'=>1,'壬'=>4,'癸'=>3],
		    '丁'=>['甲'=>5,'乙'=>6,'丙'=>7,'丁'=>8,'戊'=>9,'己'=>10,'庚'=>1,'辛'=>2,'壬'=>3,'癸'=>4],
		    '戊'=>['甲'=>4,'乙'=>3,'丙'=>6,'丁'=>5,'戊'=>8,'己'=>7,'庚'=>10,'辛'=>9,'壬'=>2,'癸'=>1],
		    '己'=>['甲'=>3,'乙'=>4,'丙'=>5,'丁'=>6,'戊'=>7,'己'=>8,'庚'=>9,'辛'=>10,'壬'=>1,'癸'=>2],
		    '庚'=>['甲'=>2,'乙'=>1,'丙'=>4,'丁'=>3,'戊'=>6,'己'=>5,'庚'=>8,'辛'=>7,'壬'=>10,'癸'=>9],
		    '辛'=>['甲'=>1,'乙'=>2,'丙'=>3,'丁'=>4,'戊'=>5,'己'=>6,'庚'=>7,'辛'=>8,'壬'=>9,'癸'=>10],
		    '壬'=>['甲'=>10,'乙'=>9,'丙'=>2,'丁'=>1,'戊'=>4,'己'=>3,'庚'=>6,'辛'=>5,'壬'=>8,'癸'=>7],
		    '癸'=>['甲'=>9,'乙'=>10,'丙'=>1,'丁'=>2,'戊'=>3,'己'=>4,'庚'=>5,'辛'=>6,'壬'=>7,'癸'=>8],
	    ];
        $shishen['tg']=[
            'year'=>$sh[$sh_table[$birth_bz['day']['tg']][$birth_bz['year']['tg']]],
            'month'=>$sh[$sh_table[$birth_bz['day']['tg']][$birth_bz['month']['tg']]],
            'day'=>'日元',
            'hour'=>$sh[$sh_table[$birth_bz['day']['tg']][$birth_bz['hour']['tg']]]
        ];

        $shishen['tg_simple']=[
            'year'=>$sh1[$sh_table[$birth_bz['day']['tg']][$birth_bz['year']['tg']]],
            'month'=>$sh1[$sh_table[$birth_bz['day']['tg']][$birth_bz['month']['tg']]],
            'day'=>'日元',
            'hour'=>$sh1[$sh_table[$birth_bz['day']['tg']][$birth_bz['hour']['tg']]]
        ];
        if($birth_zg){
            $shishen['zg_simple']=$shishen['zg']=[
                'year'=>[],
                'month'=>[],
                'day'=>[],
                'hour'=>[]
            ];
            foreach ($birth_zg as $k=>$v){
                $zg=explode('、',$v['zg']);
                foreach ($zg as $v1){
                    $shishen['zg'][$k][]=$sh[$sh_table[$birth_bz['day']['tg']][$v1]];
                    $shishen['zg_simple'][$k][]=$sh1[$sh_table[$birth_bz['day']['tg']][$v1]];
                }
            }
        }
        $data=[
            'birth_shishen'=>[
                'name'=>'十神',
                'value'=>$shishen['tg']
            ],
            'birth_shishen_simple'=>[
                'name'=>'十神(简称)',
                'value'=>$shishen['tg_simple']
            ],
            'birth_shishen_zg'=>[
                'name'=>'臧干十神',
                'value'=>(isset($shishen['zg'])?$shishen['zg']:'')
            ],
            'birth_shishen_zg_simple'=>[
                'name'=>'臧干十神(简称)',
                'value'=>(isset($shishen['zg_simple'])?$shishen['zg_simple']:'')
            ]
        ];
        return $data;
    }
    /**
     * 十神
     * @param   array           $birth_bz           八字
     * @param   string          $birth_zg           臧干
     * @return  array           $data
     */
    public function birth_shishen_simple($birth_bz,$birth_zg=''){
        $sh=[1=>'才',2=>'财',3=>'官',4=>'杀',5=>'印',6=>'枭',7=>'劫',8=>'比',9=>'伤',10=>'食'];
        //十神对照表
	    $sh_table=[
		    '甲'=>['甲'=>8,'乙'=>7,'丙'=>10,'丁'=>9,'戊'=>2,'己'=>1,'庚'=>4,'辛'=>3,'壬'=>6,'癸'=>5],
		    '乙'=>['甲'=>7,'乙'=>8,'丙'=>9,'丁'=>10,'戊'=>1,'己'=>2,'庚'=>3,'辛'=>4,'壬'=>5,'癸'=>6],
		    '丙'=>['甲'=>6,'乙'=>5,'丙'=>8,'丁'=>7,'戊'=>10,'己'=>9,'庚'=>2,'辛'=>1,'壬'=>4,'癸'=>3],
		    '丁'=>['甲'=>5,'乙'=>6,'丙'=>7,'丁'=>8,'戊'=>9,'己'=>10,'庚'=>1,'辛'=>2,'壬'=>3,'癸'=>4],
		    '戊'=>['甲'=>4,'乙'=>3,'丙'=>6,'丁'=>5,'戊'=>8,'己'=>7,'庚'=>10,'辛'=>9,'壬'=>2,'癸'=>1],
		    '己'=>['甲'=>3,'乙'=>4,'丙'=>5,'丁'=>6,'戊'=>7,'己'=>8,'庚'=>9,'辛'=>10,'壬'=>1,'癸'=>2],
		    '庚'=>['甲'=>2,'乙'=>1,'丙'=>4,'丁'=>3,'戊'=>6,'己'=>5,'庚'=>8,'辛'=>7,'壬'=>10,'癸'=>9],
		    '辛'=>['甲'=>1,'乙'=>2,'丙'=>3,'丁'=>4,'戊'=>5,'己'=>6,'庚'=>7,'辛'=>8,'壬'=>9,'癸'=>10],
		    '壬'=>['甲'=>10,'乙'=>9,'丙'=>2,'丁'=>1,'戊'=>4,'己'=>3,'庚'=>6,'辛'=>5,'壬'=>8,'癸'=>7],
		    '癸'=>['甲'=>9,'乙'=>10,'丙'=>1,'丁'=>2,'戊'=>3,'己'=>4,'庚'=>5,'辛'=>6,'壬'=>7,'癸'=>8],
	    ];
        $shishen['tg']=[
            'year'=>$sh[$sh_table[$birth_bz['day']['tg']][$birth_bz['year']['tg']]],
            'month'=>$sh[$sh_table[$birth_bz['day']['tg']][$birth_bz['month']['tg']]],
            'day'=>'日元',
            'hour'=>$sh[$sh_table[$birth_bz['day']['tg']][$birth_bz['hour']['tg']]]
        ];
        if($birth_zg){
            $shishen['zg']=[
                'year'=>[],
                'month'=>[],
                'day'=>[],
                'hour'=>[]
            ];
            foreach ($birth_zg as $k=>$v){
                $zg=explode('、',$v['zg']);
                foreach ($zg as $v1){
                    $shishen['zg'][$k][]=$sh[$sh_table[$birth_bz['day']['tg']][$v1]];
                }
            }
        }
        $data=[
            'birth_shishen'=>[
                'name'=>'十神',
                'value'=>$shishen['tg']
            ],
            'birth_shishen_zg'=>[
                'name'=>'臧干十神',
                'value'=>(isset($shishen['zg'])?$shishen['zg']:'')
            ]
        ];
        return $data;
    }
    public function jishenxiongsha($bz,$sex){
        $birth_bz[1] = $bz['year']['tg'];//年干
        $birth_bz[2] = $bz['year']['dz'];//年支
        $birth_bz[3] = $bz['month']['tg'];//月干
        $birth_bz[4] = $bz['month']['dz'];//月支
        $birth_bz[5] = $bz['day']['tg'];//日干
        $birth_bz[6] = $bz['day']['dz'];//日支
        $birth_bz[7] = $bz['hour']['tg'];//时干
        $birth_bz[8] = $bz['hour']['dz'];//时支
        $shengsha=model('base.Shengsha');
        $Shengsha = explode(':',$shengsha->shengsha($birth_bz,$sex));
        foreach($Shengsha as $Shengshak=>&$Shengshav){
            $a=explode(' ',$Shengshav);
            array_pop($a);
            $b=array_unique($a);
            $Shengshav=implode(' ',$b);
        }
        return  array(
            'name'=>'吉神凶煞',
            'value'=>$Shengsha
        );
    }
    /**
     * 星座属性
     * @param       string          $real           真实出生时间
     * @param       int             $is_info        是否查询星座点评
     * @return array
     */
    public function birth_star($real,$is_info=0){
        $date_time=strtotime($real);
        $year = date('Y',$date_time);
        $month = date('m',$date_time)+0;
        $day = date('d',$date_time)+0;
//设定星座数组
        $constellations = array(
            '摩羯', '水瓶', '双鱼', '白羊', '金牛', '双子',
            '巨蟹', '狮子', '处女', '天秤', '天蝎', '射手',);

//设定星座结束日期的数组，用于判断
        $enddays = array(19, 18, 20, 20, 20, 21, 22, 22, 22, 22, 21, 21,);
//根据月份和日期判断星座
        if($enddays[$month-1]>=$day){
            $xz=$constellations[$month-1];
        }else{
            $xz=$constellations[$month%12];
        }
        if($is_info){
            $star_db = model('db_base.SmDb')->sm_star();
            $data=$star_db[$xz.'座'];
        }else{
            $data['name']=$xz;
        }

        return array(
            'name' => '星座属性',
            'value' => $data,
        );
    }

    /**
     * 名 称:命卦属性
     * 功 能:算出命卦属性
     * @param       string      $birth      出生日期
     * @param       int         $sex        性别
     * @return      array
     **/
    public function birth_minggua($birth,$sex){
        $year=date('Y',strtotime($birth));
        $mg_data=[
            'sex0'=>['九紫离命','八白艮命','七赤兑命','六白乾命','二黑坤命','四绿巽命','三碧震命','二黑坤命','一白坎命'],
            'sex1'=>['六白乾命','七赤兑命','八白艮命','九紫离命','一白坎命','二黑坤命','三碧震命','四绿巽命','八白艮命']
        ];
        $mg=$mg_data['sex'.($sex?0:1)][($year-2)%9];
        $mginfo_db = model('db_base.SmDb')->sm_mginfo();
        $data=[
            'name' => '命卦属性',
            'value' => $mginfo_db[$mg]
        ];
        return $data;
    }
    /**
     * 日干点评
     * @param       string      $day_gz     日干支
     * @param       string      $day_tg     日干
     * @return      array
     */
    public function birth_daygan($day_gz,$day_tg){
        // 日干支点评
        $res_data = model('db_base.BaseDb')->base_gzwuxing_info();
        // 日干支层次分析
        $result = model('db_base.SmDb')->sm_gzcc();
        $data=[
            'name' => '日干支点评及层次分析',
            'value' => [
                'dp' => $res_data[$day_tg],
                'fx' => $result[$day_gz]
            ]
        ];
        return $data;
    }
    /*
    * 名 称:四季
    * 功 能:算出个人出生所在季节（如春下秋冬）
    * 参 数:@method GET @param string $brith_month 出生月份；
    * 返 回 值:array
    * 修 改: 曾德权 日期(2018/5/11)
    */
    public function birth_siji($brith_month){
        /*季节	月份	月建	旺	相	休	囚	死
        春季	2月、3月	寅卯	木	火	水	金	土
        夏季	5月、6月	巳午	火	土	木	水	金
        秋季	8月、9月	申酉	金	水	土	火	木
        冬季	11月、12月	亥子	水	木	金	土	火
        四季	4、7、10、1月	辰未戌丑	土	金	火	木	水*/
        $siji=[
            '1'=>['jijie'=>'四季','yuejian'=>'辰未戌丑','wang'=>'土','xiang'=>'金','xiu'=>'火','qiu'=>'木','si'=>'水'],
            '2'=>['jijie'=>'春季','yuejian'=>'寅卯','wang'=>'木','xiang'=>'火','xiu'=>'水','qiu'=>'金','si'=>'土'],
            '3'=>['jijie'=>'春季','yuejian'=>'寅卯','wang'=>'木','xiang'=>'火','xiu'=>'水','qiu'=>'金','si'=>'土'],
            '4'=>['jijie'=>'四季','yuejian'=>'辰未戌丑','wang'=>'土','xiang'=>'金','xiu'=>'火','qiu'=>'木','si'=>'水'],
            '5'=>['jijie'=>'夏季','yuejian'=>'巳午','wang'=>'火','xiang'=>'土','xiu'=>'木','qiu'=>'水','si'=>'金'],
            '6'=>['jijie'=>'夏季','yuejian'=>'巳午','wang'=>'火','xiang'=>'土','xiu'=>'木','qiu'=>'水','si'=>'金'],
            '7'=>['jijie'=>'四季','yuejian'=>'辰未戌丑','wang'=>'土','xiang'=>'金','xiu'=>'火','qiu'=>'木','si'=>'水'],
            '8'=>['jijie'=>'秋季','yuejian'=>'申酉','wang'=>'金','xiang'=>'水','xiu'=>'土','qiu'=>'火','si'=>'木'],
            '9'=>['jijie'=>'秋季','yuejian'=>'申酉','wang'=>'金','xiang'=>'水','xiu'=>'土','qiu'=>'火','si'=>'木'],
            '10'=>['jijie'=>'四季','yuejian'=>'辰未戌丑','wang'=>'土','xiang'=>'金','xiu'=>'火','qiu'=>'木','si'=>'水'],
            '11'=>['jijie'=>'冬季','yuejian'=>'亥子','wang'=>'水','xiang'=>'木','xiu'=>'金','qiu'=>'土','si'=>'火'],
            '12'=>['jijie'=>'冬季','yuejian'=>'亥子','wang'=>'水','xiang'=>'木','xiu'=>'金','qiu'=>'土','si'=>'火'],
        ];
//        $wuxing_wr=[
//            '金'=>['wang'=>'金','xiang'=>'水','xiu'=>'土','qiu'=>'火','si'=>'木'],
//            '水'=>['wang'=>'水','xiang'=>'木','xiu'=>'金','qiu'=>'土','si'=>''],
//            '木'=>['wang'=>'木','xiang'=>'火','xiu'=>'水','qiu'=>'金','si'=>''],
//            '火'=>['wang'=>'火','xiang'=>'土','xiu'=>'木','qiu'=>'水','si'=>''],
//            '土'=>['wang'=>'土','xiang'=>'金','xiu'=>'火','qiu'=>'木','si'=>''],
//        ];
        return [
            'name'=>'季节	月份	月建	旺	相	休	囚	死',
            'value'=>$siji[intval($brith_month)]
        ];
    }
    /*
    * 名 称: 命宫
    * 功 能: 命宫, 人命之归宿. 其起法是以子位为正月, 亥为二月, 戌为三月, 逆数至丑为十二月止. 然后把生时落在生月支上, 顺数至卯, 卯就为命宫. 命宫之天干,按生年起月法定出. 如1998年五月十七日寅时生人, 按子位正月, 亥为二月, 戌为三月, 酉为四月, 申为五月, 然后在申上起寅时, 酉上起卯时, 则酉为命宫支.再按1998戊寅年起月法, 其酉位天干为辛, 则辛酉为命宫.
    * 参 数:@method GET @param array $bazi_data 八字；
    * 返 回 值:array
    * 修 改: 曾德权 日期(2018/5/11)
    */
    public  function birth_minggong($bazi_data){
        $mdz=$bazi_data['month']['dz'];
        $hdz=$bazi_data['hour']['dz'];
        /*用命宫本数来计测算宫，现将地支按数序编出代号以便应用。*/
        $painum=array("寅"=>1,'卯'=>2,'辰'=>3,'巳'=>4,'午'=>5,'未'=>6,'申'=>7,'酉'=>8,'戌'=>9,'亥'=>10,'子'=>11,'丑'=>12);
        $ming=array(1=>"寅",2=>'卯',3=>'辰',4=>'巳',5=>'午',6=>'未',7=>'申',8=>'酉',9=>'戌',10=>'亥',11=>'子',12=>'丑');
        $minglist=array("寅"=>'寅亥','卯'=>'卯戌','辰'=>'辰酉','巳'=>'巳申','午'=>'午宫','未'=>'未宫','申'=>'巳申','酉'=>'辰酉','戌'=>'卯戌','亥'=>'寅亥','子'=>'子丑','丑'=>'子丑');
       /* 子丑宫, 土星之垣也, 生于此垣, 以土为主. 其为人也, 才擅专门, 宜习农林, 矿产, 铁路, 工程, 地质, 建筑等学科, 造诣必高.
        寅亥宫, 木星之垣也, 以木为主. 其为人也, 宜习法律, 经济, 医学等科,利于公共机构与慈善团体之活动, 如在宗教, 学校, 医院, 社会等事业.
        卯戌宫, 火星之垣也, 以火为主. 其为人也, 宜习有关理科之学术, 利于化工, 机械, 土木, 兵工一类.
        辰酉宫, 金星之垣也. 生于此垣, 以金为主. 其为人也, 多才多艺, 宜于攻习文艺, 书画, 音乐, 喜剧, 雕刻, 金石, 装饰及有关美术之学艺.
        巳申宫, 水星之垣也. 生于此垣, 以水为主, 其为人聪明才智, 宜攻读文科,利于文化教育事业. 如格局低者, 则精神每多忧郁.
        午宫, 太阳之垣, 生于此宫, 其为人也, 士农工商, 无不咸宜. 最利攻读政治, 功业自然显著, 倘格局不利, 可能发生心脏病.
        未宫, 太阴之垣, 生于此宫, 其为人也, 每多自得之情, 宜习商科, 从事航运, 经营流动性之工商业, 或办产科医院及任护士. 立命此宫, 颇有谦让之表现.*/
        $hdznum=$painum[$hdz];
        if($hdznum<2){$addnum=1;}else if($hdznum>2){$addnum=12-$hdznum+2;}else{$addnum=0;}
        $a=$painum[$mdz]-1;
        if($addnum){
            for ($i=1;$i<=$addnum;$i++){
                $a++;
                if($a==13){$a=1;}
            }
        }
        $num=$a;
        $resv=$ming[$num];
        return [
            'name'=>'命宫',
            'value'=>['mg'=>$resv,'mg2'=>$minglist[$resv]]
        ];
    }
    /**
     * 名 称: 胎元
     * 功 能:指人受精怀胎的月份. 其起法是: 人生月后紧接着这个月的天干与生月后第三个月的地支相配, 就为胎元. 如1998年八月生人, 八月为辛酉, 辛后一干是壬, 酉后第三个月地支是子, 则壬子为其胎元.
     * 胎元生命为吉, 克命不利.戊寅年辛酉月, 壬子为胎元, 戊寅纳音为土, 壬子纳音为木, 木克土不利命. 胎月见贵, 必受福荫. 刑冲破害, 决主艰辛. 鬼谷子说: "胎中如有禄, 生在富贵家, 或值空亡中, 贫穷起怨嗟." 胎元干支与生时干支纳音相生主长寿, 如果和时干支刑战克害主寿短. 又讲, 胎元临帝旺, 必然高寿.
     * @param       array       $bazi_data      八字
     * @return      array
     * 修 改: 曾德权 日期(2018/5/11)
     */
    public  function birth_taiyuan($bazi_data){
        $mtg=$bazi_data['month']['tg'];
        $mdz=$bazi_data['month']['dz'];
        $mg_tg=$this->tg_arr[$this->tg_num[$mtg]%10];
        $mg_dz=$this->dz_arr[($this->dz_num[$mdz]+2)%12];
        $data=[
            'name'=>'胎元',
            'value'=>$mg_tg.$mg_dz
        ];
        return $data;
    }
    /*
    * 名 称: 空亡
    * 功 能:以日柱为起点查空亡，终点始终为癸，日柱为乙酉开始排，乙酉、丙戌、丁亥、戊子、己丑、庚寅、辛卯、壬辰、癸巳，接下来多出来的地支为午未，则空亡为午未；以此类推。
    * 参 数:@method GET @param array $bazi_data 八字；
    * 返 回 值:array
    * 修 改: 曾德权 日期(2018/5/12)
    */
    public  function birth_kongwang($bazi_data){
        return [
            'name'=>'空亡',
            'value'=>[
                'year'=>$this->birth_kongwang2($bazi_data['year']['tg'],$bazi_data['year']['dz']),
                'month'=>$this->birth_kongwang2($bazi_data['month']['tg'],$bazi_data['month']['dz']),
                'day'=>$this->birth_kongwang2($bazi_data['day']['tg'],$bazi_data['day']['dz']),
                'hour'=>$this->birth_kongwang2($bazi_data['hour']['tg'],$bazi_data['hour']['dz'])
            ]
        ];
    }
    public  function birth_kongwang2($tg,$dz){
        $tg_num=$this->tg_num[$tg];
        $dz_num=$this->dz_num[$dz];
        $dz_run_num=10-$tg_num;
        if($dz_num+$dz_run_num<10){
            $res=$this->dz_arr[$dz_num+$dz_run_num].$this->dz_arr[$dz_num+$dz_run_num+1];
        }
        if($dz_num+$dz_run_num==10){
            ['子', '丑', '寅', '卯', '辰', '巳', '午', '未', '申', '酉', '戌', '亥'];
            $res='戌亥';
        }
        if($dz_num+$dz_run_num==11){
            ['子', '丑', '寅', '卯', '辰', '巳', '午', '未', '申', '酉', '戌', '亥'];
            $res='亥子';
        }
        if($dz_num+$dz_run_num>=12){
            $res=$this->dz_arr[$dz_num+$dz_run_num-12].$this->dz_arr[$dz_num+$dz_run_num-12+1];
        }
        return $res;
    }
    /*
    * 名 称: 胎息
    * 功 能:又称胎元，以日柱天干为基础，即与日干支相合者之干支为胎息。取与日干甲五合之己，取与日支六合之丑。己丑即为胎息。胎息之干支与胎元之干支，相生扶为吉。相冲克者属凶。若胎息有刑伤，则伤害生母，而事异母。
    * 参 数:@method GET @param array $bazi_data 八字；
    * 返 回 值:array
    * 修 改: 曾德权 日期(2018/5/12)
    */
    public  function birth_taixi($bazi_data){
        //后天胎息对照表
        $array=['甲子'=>'己丑', '乙丑'=>'庚子', '丙寅'=>'辛亥', '丁卯'=>'壬辰', '戊辰'=>'癸酉',
            '己巳'=>'甲申', '庚午'=>'乙未', '辛未'=>'丙午', '壬申'=>'丁巳', '癸酉'=>'戊辰',
            '甲戌'=>'己卯', '乙亥'=>'庚寅', '丙子'=>'辛丑', '丁丑'=>'壬子', '戊寅'=>'癸亥',
            '己卯'=>'甲戌', '庚辰'=>'乙酉', '辛巳'=>'丙申', '壬午'=>'丁未', '癸未'=>'戊午',
            '甲申'=>'己巳', '乙酉'=>'庚辰', '丙戌'=>'辛巳', '丁亥'=>'壬寅', '戊子'=>'癸丑',
            '己丑'=>'甲子', '庚寅'=>'乙亥', '门卯'=>'丙戌', '壬辰'=>'丁酉', '癸巳'=>'戊申',
            '甲午'=>'乙未', '乙未'=>'庚午', '丙申'=>'辛巳', '丁酉'=>'壬辰', '戊戌'=>'癸卯',
            '己亥'=>'甲寅', '庚子'=>'乙丑', '辛丑'=>'丙子', '壬寅'=>'丁亥', '癸卯'=>'戊戌',
            '甲辰'=>'乙酉', '乙巳'=>'庚申', '丙午'=>'辛未', '丁未'=>'壬午', '戊申'=>'癸巳',
            '己酉'=>'甲辰', '庚戌'=>'乙卯', '辛亥'=>'丙寅', '壬子'=>'丁丑', '癸丑'=>'戊子',
            '甲寅'=>'乙亥', '乙卯'=>'庚戌', '丙辰'=>'辛酉', '丁巳'=>'壬申', '戊午'=>'癸未',
            '己未'=>'甲午', '庚申'=>'乙巳', '辛酉'=>'丙辰', '壬戌'=>'丁卯', '癸亥'=>'戊寅'];
        return [
            'name'=>'胎息',
            'value'=>$array[$bazi_data['day']['tgdz']]
        ];
    }
}
