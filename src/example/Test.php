<?php

//require_once dirname(__DIR__) . '/vendor/autoload.php';

use FortuneTelling\facade\Lunar;

$year = 2023;
$month = 4;
$day = 1;
$lunarDate = Lunar::convertSolarToLunar($year, $month, $day);
print_r($lunarDate);
