<?php

public static function compareDayIsContinue($current, $next)
{
    $flag = date('Y-m-d', strtotime("$current -1 day"));

    //因为是倒叙,小于当前日期且减一天正好相等，就证明是连续的
    if ($next < $current && $next == $flag) {
        return true;
    } else {
        return false;
    }
}


//获取连续日期
public static function getUserContinuationSignCount($userId)
{
    $list = XXXXXX($userId);
    //看日期是否是连续的
    $dateList = array_column($list, 'date');
    $days = 0;
    $count = count($dateList);
    if ($count > 0) {
        for ($i = 0; $i < $count; $i++) {
            if ($i < $count - 1) {
                $res = self::compareDayIsContinue($dateList[$i], $dateList[$i + 1]);
                if ($res === true) {
                    $days = $days + 1;
                } else {
                    break;
                }
            }
        }
    }
    return $days;
}
