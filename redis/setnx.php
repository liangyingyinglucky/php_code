<?php
//增加分布式锁
use Illuminate\Support\Facades\Redis;

//用法1
$date = date('Y-m-d');
$key =  $userId . ":" . $date . ":" . $type;
$redis = Redis::connection();
lockTime:
if (!$redis->setnx($key, $userId)) {
    sleep(1);
    goto lockTime;
}
$redis->expire($key, 1);


//用法2
tryAgain:
if (!$redis->set($key, $userId, 'EX', 4, 'NX')) {//加锁并发控制
    sleep(1);
    goto tryAgain;
}
