<?php

namespace FortuneTelling\core\bz;

use FortuneTelling\data\BaGuaDb;

class BaGua
{
    private $num1;
    private $num2;
    /**
     * 设置数字
     *
     * @param int $num1
     * @param int $num2
     * @return $this
     *
     * @author wlq
     * @since 1.0 2024-06-03
     */
    public function setNums(int $num1, int $num2): self
    {
        $this->num1 = $num1;
        $this->num2 = $num2;
        return $this;
    }

    /**
     * 获取本挂
     *
     * @return array
     *
     * @author wlq
     * @since 1.0 2024-06-03
     */
    public function ben(): array
    {
        $upper = $this->getNum($this->num1);
        $lower = $this->getNum($this->num2);
        return [
            'name' => '本卦',
            'value' => [
                'guaName' => [//卦名
                              's' => BaGuaDb::NAME[$upper],//上卦
                              'x' => BaGuaDb::NAME[$lower],//下卦
                ],
                'guaXiang' => [//卦象
                               's' => BaGuaDb::BINARY[$upper],//上卦
                               'x' => BaGuaDb::BINARY[$lower],//下卦
                ],
                'guaNum' => [//卦数
                             's' => bindec(BaGuaDb::BINARY[$upper]) + 1,//上卦
                             'x' => bindec(BaGuaDb::BINARY[$lower]) + 1,//下卦
                ]
            ]
        ];
    }

    /**
     * 获取数字对应的本挂数字
     *
     * @param int $num
     * @return int
     *
     * @author wlq
     * @since 1.0 2024-06-03
     */
    private function getNum(int $num): int
    {
        return ($num + 7) % 8;
    }

    /**
     * 获取互卦
     *
     * @return array
     *
     * @author wlq
     * @since 1.0 2024-06-03
     */
    public function hu(): array
    {
        //获取上下卦数
        $upper = $this->getNum($this->num1);
        $lower = $this->getNum($this->num2);
        //获取上下卦二进制数
        $binaryS = BaGuaDb::BINARY[$upper];//上卦
        $binaryX = BaGuaDb::BINARY[$lower];//下卦
        //获取互卦二进制数
        $binaryHS = substr($binaryX . $binaryS, 2, 3);
        $binaryHX = substr($binaryX . $binaryS, 1, 3);
        //互卦二进制卦数转十进制卦数
        $upper = bindec($binaryHS);
        $lower = bindec($binaryHX);
        return [
            'name' => '互卦',
            'value' => [
                'guaName' => [//卦名
                              's' => BaGuaDb::NAME[$upper],//上卦
                              'x' => BaGuaDb::NAME[$lower],//下卦
                ],
                'guaXiang' => [//卦象
                               's' => BaGuaDb::BINARY[$upper],//上卦
                               'x' => BaGuaDb::BINARY[$lower],//下卦
                ],
                'guaNum' => [//卦数
                             's' => bindec(BaGuaDb::BINARY[$upper]) + 1,//上卦
                             'x' => bindec(BaGuaDb::BINARY[$lower]) + 1,//下卦
                ]
            ]
        ];
    }

    /**
     * 获取变卦
     *
     * @param $num
     * @return array
     *
     * @author wlq
     * @since 1.0 2024-06-03
     */
    public function bian($num): array
    {
        //获取上下卦数
        $upper = $this->getNum($this->num1);
        $lower = $this->getNum($this->num2);
        //获取上下卦二进制数
        $binaryS = BaGuaDb::BINARY[$upper];//上卦
        $binaryX = BaGuaDb::BINARY[$lower];//下卦
        $binary = $binaryX . $binaryS;
        //获取变爻位
        $yaoD = ($num + 5) % 6;
        //更改变爻位值
        $binaryD = (int)!substr($binary, $yaoD, 1);
        //生成变卦
        $binaryBian = substr($binary, 0, $yaoD) . $binaryD . substr($binary, $yaoD + 1);

        //获取变卦二进制数
        $binaryBS = substr($binaryBian, 3, 3);
        $binaryBX = substr($binaryBian, 0, 3);
        //变卦二进制卦数转十进制卦数
        $upper = bindec($binaryBS);
        $lower = bindec($binaryBX);
        return [
            'name' => '变卦',
            'value' => [
                'guaName' => [//卦名
                              's' => BaGuaDb::NAME[$upper],//上卦
                              'x' => BaGuaDb::NAME[$lower],//下卦
                ],
                'guaXiang' => [//卦象
                               's' => BaGuaDb::BINARY[$upper],//上卦
                               'x' => BaGuaDb::BINARY[$lower],//下卦
                ],
                'guaNum' => [//卦数
                             's' => bindec(BaGuaDb::BINARY[$upper]) + 1,//上卦
                             'x' => bindec(BaGuaDb::BINARY[$lower]) + 1,//下卦
                ]
            ]
        ];
    }
}
