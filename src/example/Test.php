<?php
$user = \FortuneTelling\helper\User::init('1990-01-01 00:00:01');
//获取八字
$bz = $user->baZi;
//紫微斗数命盘
$bz = $user->mp;
//获取八卦本挂
$bg = $user->bg->setNums(1, 3)->ben();
