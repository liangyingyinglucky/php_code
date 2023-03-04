<?php
//时间换算

//昨天
$time = date('Y-m-d',strtotime('-1 day'));
echo '昨天'.$time;

//前天
$time = date('Y-m-d',strtotime('-2 day'));
echo '前天'.$time;

//今天星期几
$week = date('w');
echo '今天周几'.$week;

//一周后
$time = date('Y-m-d',strtotime('+1 weeks'));
echo '一周后'.$time;

//一个月后
$time = date('Y-m-d',strtotime('+1 month'));
echo '一个月后'.$time;

//一年后
$time = date('Y-m-d',strtotime('+1 year'));
echo '一年后'.$time;

//自定义
$time = date('Y-m-d H:i:s',strtotime('+1 week 2 days 4 hours 2 seconds'));
echo '自定义'.$time;

//上个月最后一天
$month = date('Y-m-d',strtotime(date('Y-m-01',time()).'-1 day'));//本月一号减一天就是上个月的最后一天
echo $month;
//上个月最后一天是周几
$last_week = date('w',strtotime($month));
echo $last_week;

//本月的最后一个周一
$month1 = date('Y-m-d',strtotime("last Monday"));
echo '最后一个周一是'.$month1;

//上个月最后一个周三
$month = date('Y-m-d',strtotime("last Wednesday of ".date('Y-m-d',strtotime('-1 month'))));
echo '上个月最后一个周三是'.$month;


