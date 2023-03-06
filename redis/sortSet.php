<?php

use App\Models\ZZDiabetes\ZDiabetesMonthRanking;
use App\Models\ZZDiabetes\ZDiabetesUser;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

//获取总数
public static function getRankCount($teamId, $type, $date,$key)
{
    $redisServer = Redis::connection();
    $count = $redisServer->zcard($key);
    return $count;
}


public static function getUserRankList($teamId, $type, $date, $start = 1, $end = 20)
{
    $key =  $teamId . ':' . $date . ':' . $type;

    $redisServer = Redis::connection();

    $count = $redisServer->zcard($key);

    $list = [];

    if ($count > 0) {//缓存里有数据
        //超过总数的话，就不返回值
        if ($start > $count) {
            return [];
        }
        //获取列表
        $list = self::redisRankList($teamId, $type, $date, $start, $end, $redisServer, $key);

    } else {
        //缓存里没有数据
        $res = self::getUserRankListToData($teamId, $date, $type, $start, $end);

        $count = $res['count'] ?? 0;

        $list = $res['list'] ?? [];

    }


    return ['list' => $list, 'count' => $count];

}


public static function redisRankList($teamId, $type, $date, $start, $end, $redisServer = '', $key = '')
{
    if (empty($redisServer)) {
        $redisServer = Redis::connection();
    }

    if (empty($key)) {
        $key =  $teamId . ':' . $date . ':' . $type;
    }

    //获取列表
    $userList = $redisServer->zrangebyscore($key, $start, $end);

    $redisList = [];

    if (!empty($userList)) {

        //获取用户信息
        $users = XX($userList);

        if (!empty($users)) {
            $users = array_column($users, null, 'user_id');

            foreach ($userList as $uid) {
                //获取各人信息
                $rank = self::getUserRank($teamId, $type, $date, $uid, $redisServer);

                $redisList[] = array_merge($users[$uid] ?? [], $rank);

            }
        }

    }

    return $redisList;
}


public static function getUserRank($teamId, $type, $date, $userId, $redisServer = '')
{

    $key =  $teamId . ':' . $date . ':' . $type;

    if (empty($redisServer)) {
        $redisServer = Redis::connection();
    }


    //只取一个人的信息
    $userKey = $key . ':' . $userId;
    $userRank = $redisServer->hgetall($userKey);

    if (empty($userRank)) {
        //如果缓存里没有，就查表写入缓存
        $info = XXXXX($teamId, $userId, $date, $type);
        if (!empty($info)) {
            $userRank = $info->toArray();
            //$redisServer->zadd($key, $userRank['ranking_num'], $userId);
            $redisServer->hmset($userKey, $userRank);

            //$redisServer->expire($key, 604800);//7天的有效期
            $redisServer->expire($userKey, 604800);

        }

    }
    return empty($userRank) ? [] : $userRank;


}


public static function getUserRankListToData($teamId, $date, $type, $start, $end)
{

    $res = [];

    $count = self::getRealRankList($teamId, $date, $type, $res);

    $listRes = [];

    if ($count) {
        //写进去了
        //$listRes = self::redisRankList($teamId, $type, $date, $start, $end);

        $offset = $start - 1;
        $limit = $offset == 0 ? $end : ($end - $offset);
        //获取分页内的数据
        $listRes = array_slice($res, $offset, $limit);

        if (!empty($listRes)) {
            //获取用户排行
            $userIds = array_column($listRes, 'user_id');
            //获取用户信息
            $users = XXXgetUserList($userIds);

            if (!empty($users)) {
                $users = array_column($users, null, 'user_id');

                foreach ($listRes as &$value) {
                    //获取各人信息
                    $value = array_merge($value, $users[$value['user_id']] ?? []);
                }
            }
        }

    }

    return ['count' => $count, 'list' => $listRes];

}


public static function getRealRankList($teamId, $date, $type, &$listRes)
{
    //看实际的表中是否有数据
    $count = XXX::getTeamRankCount($teamId, $date, $type);
    if ($count > 0) {
        //获取数据
        $rList = XXModel($teamId, $date, $type);
        //将数据写入缓存
        foreach ($rList as $v) {
            self::setUserRank($teamId, $v);//全部写进去，防止只能读取前面的而我不在前20名
        }

        $listRes = $rList;

    }

    return $count;// 0表示实际表里也没有数据

}


public static function setUserRank($teamId, $array)
{

    try {
        $redisServer = Redis::connection();

        $keyPrefix = 'XXX' . $teamId;

        $key = $keyPrefix . ':' . $array['ranking_date'] . ':' . $array['type'];//日期+类型

        //排名信息
        $redisServer->zadd($key, $array['ranking_num'], $array['user_id']);

        $redisServer->expire($key, 604800);//7天有效期

        //排名详细信息
        $rankKey = $key . ":" . $array['user_id'];


        $redisServer->hmset($rankKey, $array);

        $redisServer->expire($rankKey, 604800);

    } catch (\Exception $e) {

    }

}







