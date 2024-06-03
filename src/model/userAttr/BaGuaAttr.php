<?php

namespace FortuneTelling\model\userAttr;

use FortuneTelling\facade\core\bz\BaGua;
use FortuneTelling\facade\core\bz\BaZi;
use FortuneTelling\facade\core\bz\ZiWei;
use FortuneTelling\helper\Lunar;

/**
 * Trait BaZiAttr
 * @package FortuneTelling\model\userAttr
 * @property  \FortuneTelling\core\bz\BaGua $bg
 */
trait BaGuaAttr
{
    /**
     * 获取命盘信息
     *
     * @return array
     *
     * @author wlq
     * @since 1.0 2023-09-18
     */
    private function getBg(): \FortuneTelling\core\bz\BaGua
    {
        if (empty($this->data['bg'])) {
            $this->data['bg'] = BaGua::getBg();
        }
        return $this->data['bg'];
    }
}