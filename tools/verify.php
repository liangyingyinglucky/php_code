<?php

function getZodiacByID($ID)
{
    if (!checkIdCard($ID)) {
        return false;
    }

    $birthYear = intval(substr($ID, 6, 4) - 1900) % 12;
    if ($birthYear < 0) {
        $birthYear += 12;
    }

    switch ($birthYear + 1) {
        case '1':
            $twelveBranches = '鼠';
            break;
        case '2':
            $twelveBranches = '牛';
            break;
        case '3':
            $twelveBranches = '虎';
            break;
        case '4':
            $twelveBranches = '兔';
            break;
        case '5':
            $twelveBranches = '龙';
            break;
        case '6':
            $twelveBranches = '蛇';
            break;
        case '7':
            $twelveBranches = '马';
            break;
        case '8':
            $twelveBranches = '羊';
            break;
        case '9':
            $twelveBranches = '猴';
            break;
        case '10':
            $twelveBranches = '鸡';
            break;
        case '11':
            $twelveBranches = '狗';
            break;
        case '12':
            $twelveBranches = '猪';
            break;
        default:
            $twelveBranches = '';
            break;
    }

    return $twelveBranches;
}




function checkChineseName($name)
{
    return preg_match('/^([\xe4-\xe9][\x80-\xbf]{2}){2,4}$/', $name) ? true : false;
}


function checkBankNo($no, $algorithm = 'Luhn')
{
    if ($algorithm == 'Luhn') {
        $str = '';
        foreach (array_reverse(str_split($no)) as $i => $c) $str .= ($i % 2 ? $c * 2 : $c);
        return array_sum(str_split($str)) % 10 == 0;
    }
    return true;
}


