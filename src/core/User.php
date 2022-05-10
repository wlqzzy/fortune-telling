<?php

namespace FortuneTelling\core;

use FortuneTelling\helper\BaZiAttr;

class User
{
    use BaZiAttr;

    protected $data = [
        'gregorianBirthday' => '',//出生日期-阳历
        'realBirthday' => '',//出生日期-阳历(计算地域偏差)
        'lunarBirthday' => '',//出生日期-阴历
        'isLeapMonth' => false,//是否为农历闰月，默认否
        'sex' => 0,
        'city' => '',
    ];

    public function __construct(array $data = [])
    {
        $mergeData = [];
        foreach ($data as $key => $value) {
            if (array_key_exists($key, $this->data)) {
                $mergeData[$key] = $value;
            }
        }
        $this->data = array_merge($this->data, $mergeData);
    }
    public function __get($name)
    {
        //空时，如果存在获取方法则设置值为获取方法返回值，否则返回原值或空字符串
        if (empty($this->data[$name])) {
            $method =  'get' . ucfirst($name);
            if (is_callable([$this, $method])) {
                $this->data[$name] = call_user_func_array([$this, 'get' . ucfirst($name)], []);
            } else {
                $this->data[$name] = $this->data[$name] ?? '';
            }
        }
        return $this->data[$name];
    }

    public function __set($name, $value)
    {
        return $this->data[$name] = $value;
    }

    public function toArray()
    {
        return $this->data;
    }

}